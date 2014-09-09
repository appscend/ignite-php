<?php

namespace Ignite\Views;
use Ignite\Element;
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
		parent::__construct($app, $viewID);

		$this->elementsContainers['elements'] = $this->prependChild(new ElementContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 'es'));
		$this->elementsContainers['elements']->view = $this;
		$this->config = $this->prependChild(new ConfigContainer());
		$this->config->appendConfigSpec('Widget/config.json');
		$this->config['view_id'] = $viewID;
		$this->config['view_type'] = 'wd';
		$this->config->view = $this;

		$this->actionsSpec = array_merge($this->actionsSpec, json_decode(file_get_contents(LIB_ROOT_DIR.ConfigContainer::CONFIG_PATH.'/'.self::ACTIONS_CONFIG_SPEC_FILE), true));
		$this->parseConfiguration(MODULES_DIR.'/'.$app->getModuleName().'/config/'.$app->getRouteName().'/'.$viewID.'.toml');
	}

	public function addView($content) {
		if (!$content instanceof WidgetView)
			$content = new WidgetViewElement('e', $content);

		if (isset($content['target_xml_path']))
			$content['target_xml_path'] = $this->app->getWebPath().$content['target_xml_path'];

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

		if (isset($content['background_image']))
			$content['background_image'] = $this->app->getAssetsPath().$content['background_image'];

		$content->view = $this;

		return $this->elementsContainers['elements']->appendChild($content);
	}

	public function addPagination($content) {
		if (!($content instanceof Element))
			$content = new Element('e', $content);

		if (isset($content['selected_image_url']))
			$content['selected_image_url'] = $this->app->getWebPath().$content['selected_image_url'];
		if (isset($content['unselected_image_url']))
			$content['unselected_image_url'] = $this->app->getWebPath().$content['unselected_image_url'];

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