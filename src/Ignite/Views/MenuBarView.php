<?php

namespace Ignite\Views;
use Ignite\Element;
use Ignite\View;
use Ignite\ElementContainer;
use Ignite\ConfigContainer;

class MenuBarView extends View {

	const ELEMENTS_CONFIG_SPEC_FILE = 'MenuBar/elements.json';

	public function __construct($app, $viewID) {
		parent::__construct($app, $viewID);

		$this->elementsContainers['elements'] = $this->prependChild(new ElementContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 'es'));
		$this->elementsContainers['elements']->view = $this;
		$this->config = $this->prependChild(new ConfigContainer());
		$this->config->appendConfigSpec('MenuBar/config.json');
		$this->config['view_id'] = $viewID;
		$this->config['view_type'] = 'mb';
		$this->config->view = $this;

		$this->pathParameters = array_merge($this->pathParameters, [
			'background_image',
			'selected_element_bg_image',
			'unselected_element_bg_image',
			'left_scroll_image',
			'right_scroll_image'
		]);

		$this->parseConfiguration();
		$this->getElementsFromConfig();
	}

	/**
	 * @param array|Element $content
	 * @return int
	 */
	public function addMenu($key = null, $content = []) {
		$element = new Element('e');

		if ($key) {
			$element['Key'] = $key;
			$keys = explode(',', $key);

			foreach ($keys as $k) {
				if (isset($this->elementClasses[trim($k)])) {
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

	public function getMenu($idx) {
		return $this->elementsContainers['elements']->getChild($idx);
	}

	public function removeMenu($idx) {
		return $this->elementsContainers['elements']->removeChild($idx);
	}

	public function getMenus() {
		return $this->elementsContainers['elements']->getChildren();
	}
} 