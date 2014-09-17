<?php

namespace Ignite\Views;
use Ignite\Element;
use Ignite\ElementContainer;
use Ignite\View;
use Ignite\ConfigContainer;

class CoverflowView extends View {

	const ELEMENTS_CONFIG_SPEC_FILE = 'CoverFlow/elements.json';
	const ACTIONS_CONFIG_SPEC_FILE = 'CoverFlow/actions.json';

	private $paramsElemPath = ['image'];

	public function __construct($app, $viewID) {
		parent::__construct($app, $viewID);

		$this->elementsContainers['elements'] = $this->prependChild(new ElementContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 'es'));
		$this->elementsContainers['elements']->view = $this;
		$this->config = $this->prependChild(new ConfigContainer());
		$this->config->appendConfigSpec('CoverFlow/config.json');
		$this->config['view_id'] = $viewID;
		$this->config['view_type'] = 'c';
		$this->config->view = $this;

		$this->actionsSpec = array_merge($this->actionsSpec, json_decode(file_get_contents(LIB_ROOT_DIR.ConfigContainer::CONFIG_PATH.'/'.self::ACTIONS_CONFIG_SPEC_FILE), true));
		$this->parseConfiguration();
		$this->getElementsFromConfig();
	}

	/**
	 * @param string $key
	 * @param array|Element $content
	 * @return Element
	 * @throws \InvalidArgumentException
	 */
	public function addImage($key = null, $content = []) {
		if (!empty($content))
			$this->processAssetsPaths($content, $this->paramsElemPath);
		$content = new Element('e', $content);

		if ($key) {
			$content['Key'] = $key;
			$keys = explode(',', $key);

			foreach ($keys as $k) {
				if (isset($this->elementClasses[trim($k)])) {

					foreach ($this->elementClasses[trim($k)] as &$prefixed)
						$this->processAssetsPaths($prefixed, $this->paramsElemPath);

					$this->applyProperties($content, $this->elementClasses[trim($k)]);
				} else {
					$this->app['ignite_logger']->log("Class '$k' is not defined in config file, in view '{$this->viewID}'.", \Ignite\Providers\Logger::LOG_WARN);
					continue;
				}
			}

		}

		$content->view = $this;

		return $this->elementsContainers['elements']->appendChild($content);
	}

	/**
	 * @param int $idx
	 * @return Element
	 */
	public function getImage($idx) {
		return $this->elementsContainers['elements']->getChild($idx);
	}

	public function removeImage($idx) {
		return $this->elementsContainers['elements']->removeChild($idx);
	}

	public function getImages() {
		return $this->elementsContainers['elements']->getChildren();
	}

} 