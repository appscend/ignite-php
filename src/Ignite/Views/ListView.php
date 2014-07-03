<?php

namespace Ignite\Views;
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

	public function addSection(array $content) {
		$content['es'] = [['e' => []]];
		$this->contents['elements']->_vars['s'][] = $content;

		return count($this->contents['elements']->_vars['s'])-1;
	}

	public function addListElement(array $content, $section) {
		$this->contents['elements']->_vars['s'][$section]['es'][0]['e'][] = $content;

		return count($this->contents['elements']->_vars['s'][$section]['es'][0]['e']);
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

	public function getListElements($section) {
		return $this->contents['elements']->_vars['s'][$section]['es'][0]['e'];
	}

} 