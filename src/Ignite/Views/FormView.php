<?php

namespace Ignite\Views;
use Ignite\Element;
use Ignite\View;
use Ignite\ViewElementsContainer;

class FormView extends View{

	const ELEMENTS_CONFIG_SPEC_FILE = 'Form/elements.json';

	public function __construct($app, $viewID) {
		parent::__construct($app, $viewID);
		$this->contents['config']->appendConfigFile('Form/config.json');
		$this->addElementContainer(new ViewElementsContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 'es'));
		$this->contents['elements']->_vars[0] = ['e' => []];
	}

	public function insertGroupSeparator($content) {
		return $this->insertElement('g', $content);
	}

	public function insertToggleSwitch($content) {
		return $this->insertElement('ts', $content);
	}

	public function insertMultiValue($content) {
		return $this->insertElement('mv', $content);
	}

	public function insertDropDown($content) {
		return $this->insertElement('p', $content);
	}

	public function insertSlider($content) {
		return $this->insertElement('s', $content);
	}

	public function insertTextField($content) {
		return $this->insertElement('tf', $content);
	}

	public function insertTextArea($content) {
		return $this->insertElement('ta', $content);
	}

	public function insertImageUpload($content) {
		return $this->insertElement('i', $content);
	}

	public function insertMapLocation($content) {
		return $this->insertElement('m', $content);
	}

	public function insertDatePicker($content) {
		return $this->insertElement('dp', $content);
	}

	public function insertButton($content) {
		return $this->insertElement('b', $content);
	}

	public function removeElement($idx) {
		return array_splice($this->contents['elements']->_vars[0]['e'], $idx, 1);
	}

	public function getElement($idx) {
		return $this->contents['elements']->_vars[0]['e'][$idx];
	}

	public function getElements() {
		return $this->contents['elements']->_vars[0]['e'];
	}

	/**
	 * @param string $type
	 * @param array|Element $content
	 * @return int
	 */
	private function insertElement($type, $content) {
		if ($content instanceof Element) {
			$this->contents['elements']->_vars[0]['e'][] = $content;
			$content['control_type'] = $type;
		} else {
			$el = new Element($content);
			$this->contents['elements']->_vars[0]['e'][] = $el;
			$el->_vars['control_type'] = $type;
		}

		return count($this->contents['elements']->_vars[0]['e'])-1;
	}

} 