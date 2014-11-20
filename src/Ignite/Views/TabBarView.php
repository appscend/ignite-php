<?php

namespace Ignite\Views;
use Ignite\Element;
use Ignite\View;
use Ignite\ElementContainer;
use Ignite\ConfigContainer;

class TabBarView extends View {

	const ELEMENTS_CONFIG_SPEC_FILE = 'TabBar/elements.json';

	private $paramsElemPath = ['image'];

	public function __construct($app, $viewID) {
		parent::__construct($app, $viewID);

		$this->elementsContainers['elements'] = $this->prependChild(new ElementContainer(self::ELEMENTS_CONFIG_SPEC_FILE));
		$this->elementsContainers['elements']->view = $this;
		$this->config = $this->prependChild(new ConfigContainer());
		$this->config->appendConfigSpec('TabBar/config.json');
		$this->config['view_id'] = $viewID;
		$this->config['view_type'] = 't';
		$this->config->view = $this;

		array_push($this->pathParameters, 'more_background_image');

		$this->parseConfiguration();
		$this->getElementsFromConfig();
	}

	/**
	 * @param array|Element $content
	 * @return int
	 */
	public function addTab($key = null, $content = []) {
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

		if (strpos($content['target_xml_path'], 'http') !== 0)
			$content['target_xml_path'] = $this->app->getView($content['target_xml_path'])->getPath($key);

		$element->appendProperties($content);
		$element->view = $this;

		return $this->elementsContainers['elements']->appendChild($element);
	}

	public function getTab($idx) {
		return $this->elementsContainers['elements']->getChild($idx);
	}


	public function removeTab($idx) {
		return $this->elementsContainers['elements']->removeChild($idx);
	}

	public function getTabs() {
		return $this->elementsContainers['elements']->getChildren();
	}

} 