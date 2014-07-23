<?php

namespace Ignite\Views;
use Ignite\Element;
use Ignite\View;
use Ignite\ElementContainer;
use Ignite\ConfigContainer;

class TabBarView extends View {

	const ELEMENTS_CONFIG_SPEC_FILE = 'TabBar/elements.json';

	public function __construct($app, $viewID) {
		parent::__construct($app);
		$this->viewID = $viewID;
		$this->elementsContainers['elements'] = $this->prependChild(new ElementContainer(self::ELEMENTS_CONFIG_SPEC_FILE));
		$this->config = $this->prependChild(new ConfigContainer());
		$this->config->appendConfigSpec('TabBar/config.json');
		$this->config['view_id'] = $viewID;
	}

	/**
	 * @param array|Element $content
	 * @return int
	 */
	public function addTab($content) {
		if (!$content instanceof Element)
			$content = new Element('tab', $content);

		$this->elementsContainers['elements']->appendChild($content);

		return count($this->elementsContainers['elements'])-1;
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