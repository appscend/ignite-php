<?php

namespace Ignite\Views;
use Ignite\ConfigContainer;
use Ignite\Element;
use Ignite\Elements\ListElement;
use Ignite\ElementContainer;
use Ignite\View;

class ListView extends View{

	const ELEMENTS_CONFIG_SPEC_FILE = 'List/elements.json';
	const ACTIONS_CONFIG_SPEC_FILE = 'List/actions.json';

	private $paramsElemPath = [
		'image_url',
		'selected_image_url',
		'selected_alternate_image_url',
		'detail_view_image_url',
		'large_image_url'
	];

	public function __construct($app, $viewID) {
		parent::__construct($app, $viewID);

		$this->elementsContainers['elements'] = $this->prependChild(new ElementContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 's'));
		$this->elementsContainers['elements']->view = $this;
		$this->config = $this->prependChild(new ConfigContainer());
		$this->config->appendConfigSpec('List/config.json');
		$this->config['view_id'] = $viewID;
		$this->config['view_type'] = 'l';
		$this->config->view = $this;

		$this->pathParameters = array_merge($this->pathParameters, [
			'drag_to_refresh_image',
			'table_header_image',
			'cell_background_image',
			'cell_selected_background_image',
			'cell_alternate_background_image',
			'cell_selected_alternate_background_image',
			'accessory_image_url',
			'accessory_selected_image_url',
			'accessory_alternate_image_url'
		]);

		$this->actionsSpec = array_merge($this->actionsSpec, json_decode(file_get_contents(LIB_ROOT_DIR.ConfigContainer::CONFIG_PATH.'/'.self::ACTIONS_CONFIG_SPEC_FILE), true));
		$this->parseConfiguration();
		$this->getElementsFromConfig();
	}

	/**
	 * @param Array|Element $content
	 * @return int
	 */
	public function addSection($key = null, $content = []) {
		if (!empty($content))
			$this->processAssetsPaths($content, $this->paramsElemPath);
		$element = new Element('e');

		if ($key) {
			$element['Key'] = $key;
			$keys = explode(',', $key);

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

		$element->appendProperties($content);
		$element->view = $this;

		return $this->elementsContainers['elements']->appendChild($element);
	}

	/**
	 * @param array|ListElement $content
	 * @param int|Element $section
	 * @return int
	 */
	public function addListElement($section, $key = null, $content = []) {
		if (!empty($content))
			$this->processAssetsPaths($content, $this->paramsElemPath);
		$element = new ListElement('e');

		if ($key) {
			$element['Key'] = $key;
			$keys = explode(',', $key);

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

		$element->appendProperties($content);
		$element->view = $this;

		return $section->appendChild($element);
	}

	public function removeSection($idx) {
		return $this->elementsContainers['elements']->removeChild($idx);
	}

	/**
	 * @param int $idx
	 * @param Element $section
	 * @return boolean
	 */
	public function removeListElement($idx, $section) {
		return $section->removeChild($idx);
	}

	public function getSections() {
		return $this->elementsContainers['elements']->getChildren();
	}

	/**
	 * @param Element $section
	 * @return Element[]
	 */
	public function getListElements($section) {
		return $section->getChildren();
	}

} 