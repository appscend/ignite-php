<?php

namespace Ignite\Views;
use Ignite\Element;
use Ignite\View;
use Ignite\ViewElementsContainer;

class CoverflowView extends View {

	const ELEMENTS_CONFIG_SPEC_FILE = 'CoverFlow/elements.json';

	public function __construct($app, $viewID) {
		parent::__construct($app, $viewID);
		$this->configFileName = 'CoverFlow/config.json';
		$this->loadSpecFile();
		$this->addElementContainer(new ViewElementsContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 'es'));
		$this->contents['elements']->_vars[0] = ['e' => []];
	}

	/**
	 * @param array|Element $content
	 * @return int
	 * @throws \InvalidArgumentException
	 */
	public function addImage($content) {
		if ($content instanceof Element) {
			$this->contents['elements']->_vars[0]['e'][] = $content;
			$content->setView($this);
		}
		else if (is_array($content)) {
			$el = new Element($content);
			$el->setView($this);
			$this->contents['elements']->_vars[0]['e'][] = $el;
		}
		else
			throw new \InvalidArgumentException("Parameter must be instance of \\Ignite\\Element or array.");

		return count($this->contents['elements']->_vars[0]['e'])-1;
	}

	/**
	 * @param int $idx
	 * @return Element
	 */
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