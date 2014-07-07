<?php

namespace Ignite\Views;
use Ignite\View;
use Ignite\ViewElementsContainer;

class FormView extends View{

	const ELEMENTS_CONFIG_SPEC_FILE = 'Form/elements.json';

	public function __construct($app, $viewID) {
		parent::__construct($app, $viewID);
		$this->configFileName = 'Form/config.json';
		$this->loadSpecFile();
		$this->addElement(new ViewElementsContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 'es'));
		$this->contents['elements']->_vars[0] = ['e' => []];
	}

	public function insertGroupSeparator(array $content) {
		return $this->insertElement('g', $content);
	}

	public function insertToggleSwitch(array $content) {
		return $this->insertElement('ts', $content);
	}

	public function insertMultiValue(array $content) {
		return $this->insertElement('mv', $content);
	}

	public function insertDropDown(array $content) {
		return $this->insertElement('p', $content);
	}

	public function insertSlider(array $content) {
		return $this->insertElement('s', $content);
	}

	public function insertTextField(array $content) {
		return $this->insertElement('tf', $content);
	}

	public function insertTextArea(array $content) {
		return $this->insertElement('ta', $content);
	}

	public function insertImageUpload(array $content) {
		return $this->insertElement('i', $content);
	}

	public function insertMapLocation(array $content) {
		return $this->insertElement('m', $content);
	}

	public function insertDatePicker(array $content) {
		return $this->insertElement('dp', $content);
	}

	public function insertButton(array $content) {
		return $this->insertElement('b', $content);
	}

	public function removeElement($idx) {
		return array_splice($this->contents['elements']->_vars[0]['e'], $idx, 1);
	}

	public function getElements() {
		return $this->contents['elements']->_vars[0]['e'];
	}

	private function insertElement($type, array $content) {
		$content['control_type'] = $type;
		$this->contents['elements']->_vars[0]['e'][] = $content;

		return count($this->contents['elements']->_vars[0]['e'])-1;
	}

} 