<?php

namespace Ignite\Views;
use Ignite\Element;
use Ignite\Elements\WidgetTextLabel;
use Ignite\Elements\WidgetViewElement;
use Ignite\Elements\WidgetImage;
use Ignite\View;
use Ignite\ElementContainer;
use Ignite\ConfigContainer;
use Ignite\ViewStub;

class WidgetView extends View{

	const ELEMENTS_CONFIG_SPEC_FILE = 'Widget/elements.json';
	const ACTIONS_CONFIG_SPEC_FILE = 'Widget/actions.json';

	private $paramsElemPath = ['background_image', 'alternate_background_image'];

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
		$this->parseConfiguration();
		$this->getElementsFromConfig();
	}

	public function addView(ViewStub $v, $key = null, $content = []) {
		$content = new WidgetViewElement('e', $content);

		if ($key) {
			$content['Key'] = $key;
			$keys = explode(',', $key);

			foreach ($keys as $k) {
				if (isset($this->elementClasses[$k])) {
					$this->applyProperties($content, $this->elementClasses[$k]);
				} else {
					$this->app['ignite_logger']->log("Class '$k' is not defined in config file, in view '{$this->viewID}'.", \Ignite\Providers\Logger::LOG_WARN);

					return false;
				}
			}

		}

		$content['target_xml_path'] = $this->app->getWebPath().'/'.$v->route;
		$content['target_view_type'] = $v->type;

		$content->view = $this;

		return $this->elementsContainers['elements']->appendChild($content);
	}

	public function addTextLabel($key = null, $content = []) {
		if (!empty($content))
			$this->processAssetsPaths($content, $this->paramsElemPath);
		$content = new WidgetTextLabel('e', $content);

		if ($key) {
			$content['Key'] = $key;
			$keys = explode(',', $key);

			foreach ($keys as $k) {
				if (isset($this->elementClasses[$k])) {

					foreach ($this->elementClasses[trim($k)] as &$prefixed)
						$this->processAssetsPaths($prefixed, $this->paramsElemPath);

					$this->applyProperties($content, $this->elementClasses[$k]);
				} else {
					$this->app['ignite_logger']->log("Class '$k' is not defined in config file, in view '{$this->viewID}'.", \Ignite\Providers\Logger::LOG_WARN);

					return false;
				}
			}
		}

		$content->view = $this;

		return $this->elementsContainers['elements']->appendChild($content);
	}

	public function addImage($key = null, $content = []) {
		if (!empty($content))
			$this->processAssetsPaths($content, $this->paramsElemPath);
		$content = new WidgetImage('e', $content);

		if ($key) {
			$content['Key'] = $key;
			$keys = explode(',', $key);

			foreach ($keys as $k) {
				if (isset($this->elementClasses[trim($k)])) {

					foreach ($this->elementClasses[trim($k)] as &$prefixed)
						$this->processAssetsPaths($prefixed, $this->paramsElemPath);

					$this->applyProperties($content, $this->elementClasses[trim($k)]);
				} else {
					$this->app['ignite_logger']->log("Class '$k' is not defined in config file, in view '{$this->viewID}'.", \Ignite\Providers\Logger::LOG_WARN);
					continue;
				}
			}
		}

		$content->view = $this;

		return $this->elementsContainers['elements']->appendChild($content);
	}

	public function addPagination($key = null, $content = []) {
		$content = new Element('e', $content);

		if ($key) {
			$content['Key'] = $key;
			$keys = explode(',', $key);

			foreach ($keys as $k) {
				if (isset($this->elementClasses[trim($k)])) {
					$this->applyProperties($content, $this->elementClasses[trim($k)]);
				} else {
					$this->app['ignite_logger']->log("Class '$k' is not defined in config file, in view '{$this->viewID}'.", \Ignite\Providers\Logger::LOG_WARN);
					continue;
				}
			}
		} else {
			$content->appendProperties($content);
		}

		$content['element_type'] = 'pagination';
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