<?php

namespace Ignite\Views;
use Ignite\View;
use Ignite\ViewElementsContainer;

class ImageGridView extends View{

	const ELEMENTS_CONFIG_SPEC_FILE = 'ImageGrid/elements.json';

	public function __construct($app, $viewID) {
		parent::__construct($app, $viewID);
		$this->configFileName = 'ImageGrid/config.json';
		$this->loadSpecFile();
		$this->addElement(new ViewElementsContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 'es'));
		$this->contents['elements']->_vars[0] = ['e' => []];
	}

	public function addImage(array $content) {
		$this->contents['elements']->_vars[0]['e'][] = $content;

		return count($this->contents['elements']->_vars[0]['e'])-1;
	}

	public function removeImage($idx) {
		return array_splice($this->contents['elements']->_vars[0]['e'], $idx, 1);
	}

	public function getImages() {
		return $this->contents['elements']->_vars[0]['e'];
	}

} 