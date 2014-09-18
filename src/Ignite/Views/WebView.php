<?php

namespace Ignite\Views;
use Ignite\Element;
use Ignite\View;
use Ignite\ElementContainer;
use Ignite\ConfigContainer;

class WebView extends View{

	const ELEMENTS_CONFIG_SPEC_FILE = 'Web/elements.json';
	const ACTIONS_CONFIG_SPEC_FILE = 'Web/actions.json';

	private $paramsElemPath = ['image', 'larger_image'];

	public function __construct($app, $viewID) {
		parent::__construct($app, $viewID);

		$this->elementsContainers['elements'] = $this->prependChild(new ElementContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 'es'));
		$this->elementsContainers['elements']->view = $this;
		$this->config = $this->prependChild(new ConfigContainer());
		$this->config->appendConfigSpec('Web/config.json');
		$this->config['view_id'] = $viewID;
		$this->config['view_type'] = 'w';
		$this->config->view = $this;

		$this->actionsSpec = array_merge($this->actionsSpec, json_decode(file_get_contents(LIB_ROOT_DIR.ConfigContainer::CONFIG_PATH.'/'.self::ACTIONS_CONFIG_SPEC_FILE), true));
		$this->parseConfiguration();
		$this->getElementsFromConfig();
	}

	/**
	 * @param array|Element $content
	 */
	public function setContent($key = null, $content = []) {
		if (!empty($content))
			$this->processAssetsPaths($content, $this->paramsElemPath);
		$element = new Element('e');

		if ($key) {
			$element['Key'] = $key;
			$keys = explode(',', $key);

			foreach ($keys as $k) {
				if (isset($this->elementClasses[trim($k)])) {

					foreach ($this->elementClasses[trim($k)] as &$prefixed)
						$this->processAssetsPaths($prefixed, $this->paramsElemPath);

					$this->applyProperties($element, $this->elementClasses[trim($k)]);
				} else {
					$this->app['ignite_logger']->log("Class '$k' is not defined in config file, in view '{$this->viewID}'.", \Ignite\Providers\Logger::LOG_WARN);
					continue;
				}
			}
		}

		$element->appendProperties($content);
		$element->view = $this;

		if (count($this->elementsContainers['elements']))
			$el = $this->elementsContainers['elements']->replaceChild($element, 0);
		else
			$el = $this->elementsContainers['elements']->appendChild($element);

		return $el;
	}

	public function getContent() {
		return $this->elementsContainers['elements']->getChild(0);
	}

} 