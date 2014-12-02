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
		$element = new WidgetViewElement('e');

		if ($key) {
			$keys = explode(',', $key);
			$element['Key'] = end($keys);

			foreach ($keys as $k) {
				if (isset($this->elementClasses[$k])) {
					$this->applyProperties($element, $this->elementClasses[$k]);
				} else {
					$this->app['ignite_logger']->log("Class '$k' is not defined in config file, in view '{$this->viewID}'.", \Ignite\Providers\Logger::LOG_WARN);
				}
			}

		}

		$element->appendProperties($content);
		$element->view = $this;
		$element['target_xml_path'] = $v->getPath($key);
		$element['target_view_type'] = $v['type'];

		return $this->elementsContainers['elements']->appendChild($element);
	}

	public function addTextLabel($key = null, $content = []) {
		if (!empty($content))
			$this->processAssetsPaths($content, $this->paramsElemPath);
		$element = new WidgetTextLabel('e');

		if ($key) {
			$keys = explode(',', $key);
			$element['Key'] = end($keys);

			foreach ($keys as $k) {
				if (isset($this->elementClasses[$k])) {

					foreach ($this->elementClasses[trim($k)] as &$prefixed)
						$this->processAssetsPaths($prefixed, $this->paramsElemPath);

					$this->applyProperties($element, $this->elementClasses[$k]);
				} else {
					$this->app['ignite_logger']->log("Class '$k' is not defined in config file, in view '{$this->viewID}'.", \Ignite\Providers\Logger::LOG_WARN);

					return false;
				}
			}
		}

		$element->appendProperties($content);
		$element->view = $this;

		return $this->elementsContainers['elements']->appendChild($element);
	}

	/**
	 * @param null $key
	 * @param array $content
	 * @return WidgetImage
	 */
	public function addImage($key = null, $content = []) {
		if (!empty($content))
			$this->processAssetsPaths($content, $this->paramsElemPath);
		$element = new WidgetImage('e');

		if ($key) {
			$keys = explode(',', $key);
			$element['Key'] = end($keys);

			foreach ($keys as $k) {
				if (isset($this->elementClasses[trim($k)])) {

					foreach ($this->elementClasses[trim($k)] as &$prefixed)
						$this->processAssetsPaths($prefixed, $this->paramsElemPath);

					$this->applyProperties($element, $this->elementClasses[trim($k)]);
				} else {
					$this->app['ignite_logger']->log("Class '$k' is not defined in config file, in view '{$this->viewID}'.", \Ignite\Providers\Logger::LOG_WARN);
					continue;
				}
			}
		}

		$element->view = $this;
		$element->appendProperties($content);

		return $this->elementsContainers['elements']->appendChild($element);
	}

	public function addPagination($key = null, $content = []) {
		$element = new Element('e');

		if ($key) {
			$keys = explode(',', $key);
			$element['Key'] = end($keys);

			foreach ($keys as $k) {
				if (isset($this->elementClasses[trim($k)])) {
					$this->applyProperties($element, $this->elementClasses[trim($k)]);
				} else {
					$this->app['ignite_logger']->log("Class '$k' is not defined in config file, in view '{$this->viewID}'.", \Ignite\Providers\Logger::LOG_WARN);
					continue;
				}
			}
		}

		$element->appendProperties($content);
		$element['element_type'] = 'pagination';
		$element->view = $this;

		return $this->elementsContainers['elements']->appendChild($element);
	}

	public function removeElement($idx) {
		return $this->elementsContainers['elements']->removeChild($idx);
	}

	public function getElements() {
		return $this->elementsContainers['elements']->getChildren();
	}
} 