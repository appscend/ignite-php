<?php

namespace Ignite\Views;
use Ignite\ConfigContainer;
use Ignite\ElementContainer;
use Ignite\View;
use Ignite\Element;

class ImageGridView extends View{

	const ELEMENTS_CONFIG_SPEC_FILE = 'ImageGrid/elements.json';

	public function __construct($app, $viewID) {
		parent::__construct($app);
		$this->viewID = $viewID;
		$this->elementsContainers['elements'] = $this->prependChild(new ElementContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 'es'));
		$this->config = $this->prependChild(new ConfigContainer());
		$this->config->appendConfigSpec('ImageGrid/config.json');
		$this->config['view_id'] = $viewID;
		$this->config['view_type'] = 'p';
	}

	/**
	 * @param array|Element $content
	 * @return int
	 */
	public function addImage($content) {
		if ($content instanceof Element) {
			$content->view = $this;
			$this->elementsContainers['elements']->appendChild($content);
		} else {
			$el = new Element('e', $content);
			$el->view = $this;
			$this->elementsContainers['elements']->appendChild($el);
		}

		return count($this->elementsContainers['elements'])-1;
	}

	public function getImage($idx) {
		return $this->elementsContainers['elements']->getChild($idx);
	}

	public function removeImage($idx) {
		return $this->elementsContainers['elements']->removeChild($idx);
	}

	public function getImages() {
		return $this->elementsContainers['elements']->getChildren();
	}

} 