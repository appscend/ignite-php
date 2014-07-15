<?php

namespace Ignite\Views;
use Ignite\View;
use Ignite\ViewElementsContainer;
use Ignite\Element;

class ImageGridView extends View{

	const ELEMENTS_CONFIG_SPEC_FILE = 'ImageGrid/elements.json';

	public function __construct($app, $viewID) {
		parent::__construct($app, $viewID);
		$this->configFileName = 'ImageGrid/config.json';
		$this->loadSpecFile();
		$this->addElement(new ViewElementsContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 'es'));
		$this->contents['elements']->_vars[0] = ['e' => []];
	}

	/**
	 * @param array|Element $content
	 * @return int
	 */
	public function addImage($content) {
		if ($content instanceof Element) {
			$this->contents['elements']->_vars[0]['e'][] = $content;
		} else {
			$el = new Element($content);
			$this->contents['elements']->_vars[0]['e'][] = $el;
		}

		return count($this->contents['elements']->_vars[0]['e'])-1;
	}

	public function getImage($idx) {
		return $this->contents['elements']->_vars[0]['e'][$idx];
	}

	public function removeImage($idx) {
		return array_splice($this->contents['elements']->_vars[0]['e'], $idx, 1);
	}

	public function getImages() {
		return $this->contents['elements']->_vars[0]['e'];
	}

} 