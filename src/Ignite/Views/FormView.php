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
		parent::__construct($app, $viewID);

		$this->elementsContainers['elements'] = $this->prependChild(new ElementContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 'es'));
		$this->elementsContainers['elements']->view = $this;
		$this->config = $this->prependChild(new ConfigContainer());
		$this->config->appendConfigSpec('Form/config.json');
		$this->config['view_id'] = $viewID;
		$this->config['view_type'] = 'fr';
		$this->config->view = $this;

		$this->actionsSpec = array_merge($this->actionsSpec, json_decode(file_get_contents(LIB_ROOT_DIR.ConfigContainer::CONFIG_PATH.'/'.self::ACTIONS_CONFIG_SPEC_FILE), true));
		$this->parseConfiguration();
		$this->getElementsFromConfig();
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
	public function insertTextField($key = null, $content = []) {
		$content = new TextFieldElement('e');

		if ($key) {
			$content['Key'] = $key;
			$keys = explode(',', $key);

			foreach ($keys as $k) {
				if (isset($this->elementClasses[trim($k)])) {
					$this->applyProperties($content, $this->elementClasses[trim($k)]);
				} else {
					$this->app['ignite_logger']->log("Class '$k' is not defined in config file, in view '{$this->viewID}'.", \Ignite\Providers\Logger::LOG_WARN);
					continue;
				}
			}
		} else {
			$content->appendProperties($content);
		}

		$content->view = $this;

		return $this->elementsContainers['elements']->appendChild($content);
	}

	public function insertTextArea($key = null, $content = []) {
		return $this->insertElement('ta', $key, $content);
	}

	public function insertImageUpload($key = null, $content = []) {
		return $this->insertElement('i', $key, $content);
	}

	public function insertMapLocation($key = null, $content = []) {
		return $this->insertElement('m', $key, $content);
	}

	public function insertDatePicker($key = null, $content = []) {
		return $this->insertElement('dp', $key, $content);
	}

	public function insertButton($key = null, $content = []) {
		return $this->insertElement('b', $key, $content);
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
	private function insertElement($type, $key = null, $content = []) {
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

		$content['control_type'] = $type;
		$content->view = $this;

		return $this->elementsContainers['elements']->appendChild($content);
	}

} 