<?php

namespace Ignite\Views;
use Ignite\Element;
use Ignite\View;
use Ignite\ElementContainer;
use Ignite\ConfigContainer;

class MenuBarView extends View {

	const ELEMENTS_CONFIG_SPEC_FILE = 'MenuBar/elements.json';

	public function __construct($app, $viewID) {
		parent::__construct($app);
		$this->viewID = $viewID;
		$this->elementsContainers['elements'] = $this->prependChild(new ElementContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 'es'));
		$this->config = $this->prependChild(new ConfigContainer());
		$this->config->appendConfigSpec('MenuBar/config.json');
		$this->config['view_id'] = $viewID;
	}

	/**
	 * @param array|Element $content
	 * @return int
	 */
	public function addMenu($content) {
		if (!$content instanceof Element)
			$content = new Element('e', $content);

		$this->elementsContainers['elements']->appendChild($content);

		return count($this->elementsContainers['elements'])-1;
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