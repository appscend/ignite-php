<?php

namespace Ignite\Views;
use Ignite\ConfigContainer;
use Ignite\Element;
use Ignite\ElementContainer;
use Ignite\View;

class ListView extends View{

	const ELEMENTS_CONFIG_SPEC_FILE = 'List/elements.json';

	public function __construct($app, $viewID) {
		parent::__construct($app);
		$this->viewID = $viewID;
		$this->elementsContainers['elements'] = $this->prependChild(new ElementContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 's'));
		$this->config = $this->prependChild(new ConfigContainer());
		$this->config->appendConfigSpec('List/config.json');
		$this->config['view_id'] = $viewID;
	}

	/**
	 * @param Array|Element $content
	 * @return int
	 */
	public function addSection($content) {
		if ($content instanceof Element) {
			$this->elementsContainers['elements']->appendChild($content);
		} else {
			$el = new Element('es', $content);
			$this->elementsContainers['elements']->appendChild($el);
		}

		return count($this->elementsContainers['elements'])-1;
	}

	/**
	 * @param array|Element $content
	 * @param int|Element $section
	 * @return int
	 */
	public function addListElement($content, $section) {
		if (!$content instanceof Element)
			$content = new Element('e', $content);

		if (!$section instanceof Element)
			$section = $this->elementsContainers['elements']->getChild($section);

		$section->appendChild($content);

		return count($section);
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