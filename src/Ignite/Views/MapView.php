<?php

namespace Ignite\Views;
use Ignite\ConfigContainer;
use Ignite\Element;
use Ignite\ElementContainer;
use Ignite\View;

class MapView extends View{

	const ELEMENTS_CONFIG_SPEC_FILE = 'Map/elements.json';
	const ACTIONS_CONFIG_SPEC_FILE = 'Map/actions.json';

	private $paramsElemPath = ['image'];

	public function __construct($app, $viewID) {
		parent::__construct($app, $viewID);

		$this->elementsContainers['elements'] = $this->prependChild(new ElementContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 'es'));
		$this->elementsContainers['elements']->view = $this;
		$this->config = $this->prependChild(new ConfigContainer());
		$this->config->appendConfigSpec('Map/config.json');
		$this->config['view_id'] = $viewID;
		$this->config['view_type'] = 'm';
		$this->config->view = $this;

		array_push($this->pathParameters, 'pin_image');

		$this->actionsSpec = array_merge($this->actionsSpec, json_decode(file_get_contents(LIB_ROOT_DIR.ConfigContainer::CONFIG_PATH.'/'.self::ACTIONS_CONFIG_SPEC_FILE), true));
		$this->parseConfiguration();
		$this->getElementsFromConfig();
	}

	/**
	 * @param array|Element $content
	 * @return int
	 */
	public function addLocation($key = null, $content = []) {
		if (!empty($content))
			$this->processAssetsPaths($content, $this->paramsElemPath);
		$element = new Element('e');

		if ($key) {
			$keys = explode(',', $key);
			$element['Key'] = end($keys);

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

		return $this->elementsContainers['elements']->appendChild($element);
	}

	/**
	 * @param int $idx
	 */
	public function getLocation($idx) {
		return $this->elementsContainers['elements']->getChild($idx);
	}

	public function removeLocation($idx) {
		return $this->elementsContainers['elements']->removeChild($idx);
	}

	public function getLocations() {
		return $this->elementsContainers['elements']->getChildren();
	}

} 