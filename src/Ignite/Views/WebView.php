<?php

namespace Ignite\Views;
use Ignite\Element;
use Ignite\View;
use Ignite\ElementContainer;
use Ignite\ConfigContainer;

class WebView extends View{

	const ELEMENTS_CONFIG_SPEC_FILE = 'Web/elements.json';
	const ACTIONS_CONFIG_SPEC_FILE = 'Web/actions.json';

	public function __construct($app, $viewID) {
		parent::__construct($app);
		$this->viewID = $viewID;
		$this->elementsContainers['elements'] = $this->prependChild(new ElementContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 'es'));
		$this->elementsContainers['elements']->view = $this;
		$this->config = $this->prependChild(new ConfigContainer());
		$this->config->appendConfigSpec('Web/config.json');
		$this->config['view_id'] = $viewID;
		$this->config['view_type'] = 'w';
		$this->config->view = $this;

		$this->actionsSpec = array_merge($this->actionsSpec, json_decode(file_get_contents(ROOT_DIR.ConfigContainer::CONFIG_PATH.'/'.self::ACTIONS_CONFIG_SPEC_FILE), true));
	}

	/**
	 * @param array|Element $content
	 */
	public function setContent($content) {
		if (!$content instanceof Element)
			$content = new Element($content);

		if (count($this->elementsContainers['elements']))
			$this->elementsContainers['elements']->replaceChild($content, 0);
		else
			$this->elementsContainers['elements']->appendChild($content);

		$content->view = $this;
	}

	public function getContent() {
		return $this->elementsContainers['elements']->getChild(0);
	}

} 