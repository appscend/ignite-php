<?php

namespace Ignite\Views;
use Ignite\Element;
use Ignite\View;
use Ignite\ElementContainer;
use Ignite\ConfigContainer;

class WidgetView extends View{

	const ELEMENTS_CONFIG_SPEC_FILE = 'Widget/elements.json';

	public function __construct($app, $viewID) {
		parent::__construct($app);
		$this->viewID = $viewID;
		$this->elementsContainers['elements'] = $this->prependChild(new ElementContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 'es'));
		$this->config = $this->prependChild(new ConfigContainer());
		$this->config->appendConfigSpec('Widget/config.json');
		$this->config['view_id'] = $viewID;
	}

	public function addViewElement($content) {
		if (!$content instanceof Element)
			$content = new Element('e', $content);

		$this->elementsContainers['elements']->appendChild($content);

		return count($this->elementsContainers['elements'])-1;
	}

	public function addTextLabel($content) {
		if (!$content instanceof Element)
			$content = new Element('e', $content);

		$content['element_type'] = 'label';
		$this->elementsContainers['elements']->appendChild($content);

		return count($this->elementsContainers['elements'])-1;
	}

	public function addImage($content) {
		if (!$content instanceof Element)
			$content = new Element('e', $content);

		$content['element_type'] = 'image';
		$this->elementsContainers['elements']->appendChild($content);

		return count($this->elementsContainers['elements'])-1;
	}

	public function removeElement($idx) {
		return $this->elementsContainers['elements']->removeChild($idx);
	}

	public function getElements() {
		return $this->elementsContainers['elements']->getChildren();
	}
} 