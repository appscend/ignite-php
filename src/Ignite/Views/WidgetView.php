<?php

namespace Ignite\Views;
use Ignite\View;
use Ignite\ViewElementsContainer;

class WidgetView extends View{

	const ELEMENTS_CONFIG_SPEC_FILE = 'Widget/elements.json';

	public function __construct($app, $viewID) {
		parent::__construct($app, $viewID);
		$this->configFileName = 'Widget/config.json';
		$this->loadSpecFile();
		$this->addElement(new ViewElementsContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 'es'));
		$this->contents['elements']->_vars[0] = ['e' => []];
	}

	public function addViewElement(array $content) {
		$this->contents['elements']->_vars[0]['e'][] = $content;

		return count($this->contents['elements']->_vars[0]['e'])-1;
	}

	public function addTextLabel(array $content) {
		$content['element_type'] = 'label';
		$this->contents['elements']->_vars[0]['e'][] = $content;

		return count($this->contents['elements']->_vars[0]['e'])-1;
	}

	public function addImage(array $content) {
		$content['element_type'] = 'image';
		$this->contents['elements']->_vars[0]['e'][] = $content;

		return count($this->contents['elements']->_vars[0]['e'])-1;
	}

	public function removeElement($idx) {
		return array_splice($this->contents['elements']->_vars[0]['e'], $idx, 1);
	}

	public function getElements() {
		return $this->contents['elements']->_vars[0]['e'];
	}
} 