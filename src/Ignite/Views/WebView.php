<?php

namespace Ignite\Views;
use Ignite\View;
use Ignite\ViewElementsContainer;


class WebView extends View{

	const ELEMENTS_CONFIG_SPEC_FILE = 'Web/elements.json';

	public function __construct($app, $viewID) {
		parent::__construct($app, $viewID);
		$this->configFileName = 'Web/config.json';
		$this->loadSpecFile();
		$this->addElement(new ViewElementsContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 'es'));
		$this->contents['elements']->_vars[0] = [];
	}

	public function setContent(array $content) {
		$this->contents['elements']->_vars[0]['e'] = $content;
	}

	public function getContent() {
		return $this->contents['elements']->_vars[0]['e'];
	}

} 