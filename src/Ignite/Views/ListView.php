<?php

namespace Ignite\Views;
use Ignite\ConfigContainer;
use Ignite\Element;
use Ignite\Elements\ListElement;
use Ignite\ElementContainer;
use Ignite\View;

class ListView extends View{

	const ELEMENTS_CONFIG_SPEC_FILE = 'List/elements.json';
	const ACTIONS_CONFIG_SPEC_FILE = 'List/actions.json';

	public function __construct($app, $viewID) {
		parent::__construct($app);
		$this->viewID = $viewID;
		$this->elementsContainers['elements'] = $this->prependChild(new ElementContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 's'));
		$this->elementsContainers['elements']->view = $this;
		$this->config = $this->prependChild(new ConfigContainer());
		$this->config->appendConfigSpec('List/config.json');
		$this->config['view_id'] = $viewID;
		$this->config['view_type'] = 'l';
		$this->config->view = $this;

		$this->actionsSpec = array_merge($this->actionsSpec, json_decode(file_get_contents(ROOT_DIR.ConfigContainer::CONFIG_PATH.'/'.self::ACTIONS_CONFIG_SPEC_FILE), true));
	}

	/**
	 * @param Array|Element $content
	 * @return int
	 */
	public function addSection($content) {
		if (!$content instanceof Element)
			$content = new Element('es', $content);

		$content->view = $this;


		return $this->elementsContainers['elements']->appendChild($content);
	}

	/**
	 * @param array|ListElement $content
	 * @param int|Element $section
	 * @return int
	 */
	public function addListElement($content, $section) {
		if (!$content instanceof ListElement)
			$content = new Element('e', $content);

		if (!$section instanceof Element)
			$section = $this->elementsContainers['elements']->getChild($section);

		$content->view = $this;

		return $section->appendChild($content);
	}

	public function removeSection($idx) {
		return $this->elementsContainers['elements']->removeChild($idx);
	}

	/**
	 * @param int $idx
	 * @param Element $section
	 * @return boolean
	 */
	public function removeListElement($idx, $section) {
		return $section->removeChild($idx);
	}

	public function getSections() {
		return $this->elementsContainers['elements']->getChildren();
	}

	/**
	 * @param Element $section
	 * @return Element[]
	 */
	public function getListElements($section) {
		return $section->getChildren();
	}

} 