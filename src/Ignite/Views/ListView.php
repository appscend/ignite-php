<?php

namespace Ignite\Views;
use Ignite\Element;
use Ignite\View;
use Ignite\ViewElementsContainer;

class ListView extends View{

	const ELEMENTS_CONFIG_SPEC_FILE = 'List/elements.json';

	public function __construct($app, $viewID) {
		parent::__construct($app, $viewID);
		$this->configFileName = 'List/config.json';
		$this->loadSpecFile();
		$this->addElement(new ViewElementsContainer(self::ELEMENTS_CONFIG_SPEC_FILE));
		$this->contents['elements']->_vars['s'] = [];
	}

	/**
	 * @param Array|Element $content
	 * @return int
	 */
	public function addSection($content) {
		if ($content instanceof Element) {
			$this->contents['elements']->_vars['s'][] = $content;
		} else {
			$el = new Element($content);
			$this->contents['elements']->_vars['s'][] = $el;
		}

		//$content['es'] = [['e' => []]];
		//$this->contents['elements']->_vars['s'][] = $content;

		return count($this->contents['elements']->_vars['s'])-1;
	}

	/**
	 * @param array|Element $content
	 * @param int|Element $section
	 * @return int
	 */
	public function addListElement($content, $section) {
		if ($content instanceof Element) {
			if (!$section instanceof Element)
				$section = $this->contents['elements']->_vars['s'][$section];

			if (!isset($section->_vars['es']))
				$section->_vars['es'] = ['e' => []];

		} else {
			$content = new Element($content);
		}

		$section->_vars['es'][0]['e'][] = $content;

		return count($section->_vars['es'][0]['e']);
	}

	public function removeSection($idx) {
		return array_splice($this->contents['elements']->_vars['s'], $idx, 1);
	}

	public function removeListElement($idx, $section) {
		return array_splice($this->contents['elements']->_vars['s'][$section]['es'][0]['e'], $idx, 1);
	}

	public function getSections() {
		return $this->contents['elements']->_vars['s'];
	}

	/**
	 * @param int|Element $section
	 * @return Element[]
	 */
	public function getListElements($section) {
		if (!$section instanceof Element)
			$section = $this->contents['elements']->_vars['s'][$section];

		return $section->_vars['es'][0]['e'];
	}

} 