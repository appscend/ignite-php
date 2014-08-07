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
		$this->elementsContainers['elements']->view = $this;
		$this->config = $this->prependChild(new ConfigContainer());
		$this->config->appendConfigSpec('CoverFlow/config.json');
		$this->config['view_id'] = $viewID;
		$this->config['view_type'] = 'c';
		$this->config->view = $this;

		$this->actionsSpec = array_merge($this->actionsSpec, json_decode(file_get_contents(ROOT_DIR.ConfigContainer::CONFIG_PATH.'/'.self::ACTIONS_CONFIG_SPEC_FILE), true));
	}

	/**
	 * @param array|Element $content
	 * @return Element
	 * @throws \InvalidArgumentException
	 */
	public function addImage($content) {
		if ($content instanceof Element)
			$content->setTag('e');
		else if (is_array($content))
			$content = new Element('e', $content);
		else
			throw new \InvalidArgumentException("Parameter must be instance of \\Ignite\\Element or array.");

		$content->view = $this;
		if (isset($content['image']))
			$content['image'] = $this->app->getAssetsPath().$content['image'];

		return $this->elementsContainers['elements']->appendChild($content);
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