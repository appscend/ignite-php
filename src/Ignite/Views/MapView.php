<?php

namespace Ignite\Views;
use Ignite\Element;
use Ignite\View;
use Ignite\ViewElementsContainer;

class MapView extends View{

	const ELEMENTS_CONFIG_SPEC_FILE = 'Map/elements.json';

	public function __construct($app, $viewID) {
		parent::__construct($app, $viewID);
		$this->configFileName = 'Map/config.json';
		$this->loadSpecFile();
		$this->addElementContainer(new ViewElementsContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 'es'));
		$this->contents['elements']->_vars[0] = ['e' => []];
	}

	/**
	 * @param array|Element $content
	 * @return int
	 */
	public function addLocation($content) {
		if (!$content instanceof Element)
			$content = new Element($content);

		$this->contents['elements']->_vars[0]['e'][] = $content;

		return count($this->contents['elements']->_vars[0]['e'])-1;
	}

	/**
	 * @param int $idx
	 */
	public function getLocation($idx) {
		return $this->contents['elements']->_vars[0]['e'][$idx];
	}

	public function removeLocation($idx) {
		return array_splice($this->contents['elements']->_vars[0]['e'], $idx, 1);
	}

	public function getLocations() {
		return $this->contents['elements']->_vars[0]['e'];
	}

} 