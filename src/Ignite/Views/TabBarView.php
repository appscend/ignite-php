<?php

namespace Ignite\Views;
use Ignite\Element;
use Ignite\View;
use Ignite\ElementContainer;
use Ignite\ConfigContainer;

class TabBarView extends View {

	const ELEMENTS_CONFIG_SPEC_FILE = 'TabBar/elements.json';

	public function __construct($app, $viewID) {
		parent::__construct($app, $viewID);

		$this->elementsContainers['elements'] = $this->prependChild(new ElementContainer(self::ELEMENTS_CONFIG_SPEC_FILE));
		$this->elementsContainers['elements']->view = $this;
		$this->config = $this->prependChild(new ConfigContainer());
		$this->config->appendConfigSpec('TabBar/config.json');
		$this->config['view_id'] = $viewID;
		$this->config['view_type'] = 't';
		$this->config->view = $this;
		$this->parseConfiguration();
		$this->getElementsFromConfig();
	}

	/**
	 * @param array|Element $content
	 * @return int
	 */
	public function addTab($key = null, $content = null) {
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