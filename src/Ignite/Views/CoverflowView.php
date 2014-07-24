<?php

namespace Ignite\Views;
use Ignite\Element;
use Ignite\ElementContainer;
use Ignite\View;
use Ignite\ConfigContainer;

class CoverflowView extends View {

	const ELEMENTS_CONFIG_SPEC_FILE = 'CoverFlow/elements.json';
	const ACTIONS_CONFIG_SPEC_FILE = 'CoverFlow/actions.json';

	public function __construct($app, $viewID) {
		parent::__construct($app);
		$this->viewID = $viewID;

		$this->elementsContainers['elements'] = $this->prependChild(new ElementContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 'es'));
		$this->config = $this->prependChild(new ConfigContainer());
		$this->config->appendConfigSpec('CoverFlow/config.json');
		$this->config['view_id'] = $viewID;
		$this->config['view_type'] = 'c';

		$this->actionsSpec = array_merge($this->actionsSpec, json_decode(file_get_contents(ROOT_DIR.ConfigContainer::CONFIG_PATH.'/'.self::ACTIONS_CONFIG_SPEC_FILE), true));
	}

	/**
	 * @param array|Element $content
	 * @return int
	 * @throws \InvalidArgumentException
	 */
	public function addImage($content) {
		if ($content instanceof Element) {
			$this->elementsContainers['elements']->appendChild($content);
			$content->setTag('e');
			$content->view = $this;
		}
		else if (is_array($content)) {
			$el = new Element('e', $content);
			$el->view = $this;
			$this->elementsContainers['elements']->appendChild($el);
		}
		else
			throw new \InvalidArgumentException("Parameter must be instance of \\Ignite\\Element or array.");

		return count($this->elementsContainers['elements'])-1;
	}

	/**
	 * @param int $idx
	 * @return Element
	 */
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