<?php

namespace Ignite\Views;
use Ignite\ConfigContainer;
use Ignite\Element;
use Ignite\ElementContainer;
use Ignite\View;

class MapView extends View{

	const ELEMENTS_CONFIG_SPEC_FILE = 'Map/elements.json';
	const ACTIONS_CONFIG_SPEC_FILE = 'Map/actions.json';

	public function __construct($app, $viewID) {
		parent::__construct($app);
		$this->viewID = $viewID;
		$this->elementsContainers['elements'] = $this->prependChild(new ElementContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 'es'));
		$this->elementsContainers['elements']->view = $this;
		$this->config = $this->prependChild(new ConfigContainer());
		$this->config->appendConfigSpec('Map/config.json');
		$this->config['view_id'] = $viewID;
		$this->config['view_type'] = 'm';
		$this->config->view = $this;

		$this->actionsSpec = array_merge($this->actionsSpec, json_decode(file_get_contents(ROOT_DIR.ConfigContainer::CONFIG_PATH.'/'.self::ACTIONS_CONFIG_SPEC_FILE), true));
	}

	/**
	 * @param array|Element $content
	 * @return int
	 */
	public function addLocation($content) {
		if (!$content instanceof Element)
			$content = new Element('e', $content);

		$this->elementsContainers['elements']->appendChild($content);
		$content->view = $this;

		return count($this->elementsContainers['elements'])-1;
	}

	/**
	 * @param int $idx
	 */
	public function getLocation($idx) {
		return $this->elementsContainers['elements']->getChild($idx);
	}

	public function removeLocation($idx) {
		return $this->elementsContainers['elements']->removeChild($idx);
	}

	public function getLocations() {
		return $this->elementsContainers['elements']->getChildren();
	}

} 