<?php

namespace Ignite\Views;
use Ignite\ConfigContainer;
use Ignite\Element;
use Ignite\Elements\TextFieldElement;
use Ignite\ElementContainer;
use Ignite\View;

class FormView extends View{

	const ELEMENTS_CONFIG_SPEC_FILE = 'Form/elements.json';
	const ACTIONS_CONFIG_SPEC_FILE = 'Form/actions.json';

	public function __construct($app, $viewID) {
		parent::__construct($app);
		$this->viewID = $viewID;
		$this->elementsContainers['elements'] = $this->prependChild(new ElementContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 'es'));
		$this->elementsContainers['elements']->view = $this;
		$this->config = $this->prependChild(new ConfigContainer());
		$this->config->appendConfigSpec('Form/config.json');
		$this->config['view_id'] = $viewID;
		$this->config['view_type'] = 'fr';
		$this->config->view = $this;

		$this->actionsSpec = array_merge($this->actionsSpec, json_decode(file_get_contents(ROOT_DIR.ConfigContainer::CONFIG_PATH.'/'.self::ACTIONS_CONFIG_SPEC_FILE), true));
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

	/**
	 * @param array|TextFieldElement $content
	 * @return int
	 */
	public function insertTextField($content) {
		if ($content instanceof TextFieldElement) {
			$this->elementsContainers['elements']->appendChild($content);
			$content->view = $this;
		} else {
			$el = new Element('e', $content);
			$this->elementsContainers['elements']->appendChild($el);
			$el->view = $this;
		}

		return count($this->elementsContainers['elements'])-1;
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
		return $this->elementsContainers['elements']->removeChild($idx);
	}

	public function getElement($idx) {
		return $this->elementsContainers['elements']->getChild($idx);
	}

	public function getElements() {
		return $this->elementsContainers['elements']->getChildren();
	}

	/**
	 * @param string $type
	 * @param array|Element $content
	 * @return int
	 */
	private function insertElement($type, $content) {
		if ($content instanceof Element) {
			$this->elementsContainers['elements']->appendChild($content);
			$content['control_type'] = $type;
			$content->view = $this;
		} else {
			$el = new Element('e', $content);
			$this->elementsContainers['elements']->appendChild($el);
			$el['control_type'] = $type;
			$el->view = $this;
		}

		return count($this->elementsContainers['elements'])-1;
	}

} 