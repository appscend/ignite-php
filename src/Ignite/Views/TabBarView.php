<?php

namespace Ignite\Views;
use Ignite\View;
use Ignite\ViewElementsContainer;

class TabBarView extends View {

	const ELEMENTS_CONFIG_SPEC_FILE = 'TabBar/elements.json';

	public function __construct($app, $viewID) {
		parent::__construct($app, $viewID);
		$this->configFileName = 'TabBar/config.json';
		$this->loadSpecFile();
		$this->addElement(new ViewElementsContainer(self::ELEMENTS_CONFIG_SPEC_FILE));
		$this->contents['elements']->_vars['tab'] = [];
	}

	public function addTab(array $content) {
		$this->contents['elements']->_vars['tab'][] = $content;

		return count($this->contents['elements']->_vars['tab'])-1;
	}

	public function removeTab($idx) {
		return array_splice($this->contents['elements']->_vars['tab'], $idx, 1);
	}

	public function getTabs() {
		return $this->contents['elements']->_vars['tab'];
	}

} 