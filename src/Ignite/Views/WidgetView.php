<?php

namespace Ignite\Views;
use Ignite\Elements\WidgetTextLabel;
use Ignite\Elements\WidgetViewElement;
use Ignite\Elements\WidgetImage;
use Ignite\View;
use Ignite\ElementContainer;
use Ignite\ConfigContainer;

class WidgetView extends View{

	const ELEMENTS_CONFIG_SPEC_FILE = 'Widget/elements.json';
	const ACTIONS_CONFIG_SPEC_FILE = 'Widget/actions.json';

	public function __construct($app, $viewID) {
		parent::__construct($app);
		$this->viewID = $viewID;
		$this->elementsContainers['elements'] = $this->prependChild(new ElementContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 'es'));
		$this->elementsContainers['elements']->view = $this;
		$this->config = $this->prependChild(new ConfigContainer());
		$this->config->appendConfigSpec('Widget/config.json');
		$this->config['view_id'] = $viewID;
		$this->config['view_type'] = 'wd';
		$this->config->view = $this;

		$this->actionsSpec = array_merge($this->actionsSpec, json_decode(file_get_contents(ROOT_DIR.ConfigContainer::CONFIG_PATH.'/'.self::ACTIONS_CONFIG_SPEC_FILE), true));

	}

	public function addView($content) {
		if (!$content instanceof WidgetView)
			$content = new WidgetViewElement('e', $content);

		$content->view = $this;

		return $this->elementsContainers['elements']->appendChild($content);
	}

	public function addTextLabel($content) {
		if (!$content instanceof WidgetTextLabel)
			$content = new WidgetTextLabel('e', $content);

		$content->view = $this;

		return $this->elementsContainers['elements']->appendChild($content);
	}

	public function addImage($content) {
		if (!$content instanceof WidgetImage)
			$content = new WidgetImage('e', $content);

		$content->view = $this;

		return $this->elementsContainers['elements']->appendChild($content);
	}

	public function removeElement($idx) {
		return $this->elementsContainers['elements']->removeChild($idx);
	}

	public function getElements() {
		return $this->elementsContainers['elements']->getChildren();
	}
} 