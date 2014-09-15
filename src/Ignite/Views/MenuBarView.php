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
		$this->parseConfiguration();
		$this->getElementsFromConfig();
	}

	/**
	 * @param array|Element $content
	 * @return int
	 */
	public function addMenu($key = null, $content = null) {
		$content = new Element('e');

		if ($key) {
			$content['Key'] = $key;
			if (isset($this->elementClasses[$key])) {
				$this->applyProperties($content, $this->elementClasses[$key]);
			} else {
				$this->app['ignite_logger']->log("Class '$key' is not defined in config file, in view '{$this->viewID}'.", \Ignite\Providers\Logger::LOG_WARN);

				return false;
			}

		} else {
			$content->appendProperties($content);
		}

		$content->view = $this;

		return $this->elementsContainers['elements']->appendChild($content);
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