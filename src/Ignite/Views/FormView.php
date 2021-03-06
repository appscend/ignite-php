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

		array_push($this->pathParameters, 'control_cell_background_image');

		$this->actionsSpec = array_merge($this->actionsSpec, json_decode(file_get_contents(LIB_ROOT_DIR.ConfigContainer::CONFIG_PATH.'/'.self::ACTIONS_CONFIG_SPEC_FILE), true));
		$this->parseConfiguration();
		$this->getElementsFromConfig();
	}

	public function insertGroupSeparator($content) {
		return $this->insertElement('g', $content);
	}

	public function insertToggleSwitch($content) {
		$this->processAssetsPaths($content, ['image_true', 'image_true']);
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
		$element = new TextFieldElement('e');

		if ($key) {
			$keys = explode(',', $key);
			$element['Key'] = end($keys);

			foreach ($keys as $k) {
				if (isset($this->elementClasses[trim($k)])) {
					$this->applyProperties($element, $this->elementClasses[trim($k)]);
				} else {
					$this->app['ignite_logger']->log("Class '$k' is not defined in config file, in view '{$this->viewID}'.", \Ignite\Providers\Logger::LOG_WARN);
					continue;
				}
			}
		}

		$element->appendProperties($content);
		$element->view = $this;

		return $this->elementsContainers['elements']->appendChild($element);
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
		$element = new Element('e');

		if ($key) {
			$keys = explode(',', $key);
			$element['Key'] = end($keys);

			foreach ($keys as $k) {
				if (isset($this->elementClasses[$k])) {
					$this->applyProperties($element, $this->elementClasses[$k]);
				} else {
					$this->app['ignite_logger']->log("Class '$k' is not defined in config file, in view '{$this->viewID}'.", \Ignite\Providers\Logger::LOG_WARN);

					return false;
				}
			}

		}

		$element->appendProperties($content);
		$element['control_type'] = $type;
		$element->view = $this;

		return $this->elementsContainers['elements']->appendChild($element);
	}

} 