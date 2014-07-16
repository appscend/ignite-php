<?php

namespace Ignite\Views;
use Ignite\Element;
use Ignite\View;
use Ignite\ViewElementsContainer;

class MenuBarView extends View {

	const ELEMENTS_CONFIG_SPEC_FILE = 'MenuBar/elements.json';

	public function __construct($app, $viewID) {
		parent::__construct($app, $viewID);
		$this->configFileName = 'MenuBar/config.json';
		$this->loadSpecFile();
		$this->addElementContainer(new ViewElementsContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 'es'));
		$this->contents['elements']->_vars[0] = ['e' => []];
	}

	/**
	 * @param array|Element $content
	 * @return int
	 */
	public function addMenu($content) {
		if (!$content instanceof Element)
			$content = new Element($content);

		$this->contents['elements']->_vars[0]['e'][] = $content;

		return count($this->contents['elements']->_vars[0]['e'])-1;
	}

	public function getMenu($idx) {
		return $this->contents['elements']->_vars[0]['e'][$idx];
	}

	public function removeMenu($idx) {
		return array_splice($this->contents['elements']->_vars[0]['e'], $idx, 1);
	}

	public function getMenus() {
		return $this->contents['elements']->_vars[0]['e'];
	}
} 