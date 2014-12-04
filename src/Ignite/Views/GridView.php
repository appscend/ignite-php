<?php

namespace Ignite\Views;

use Ignite\ConfigContainer;
use Ignite\ElementContainer;
use Ignite\View;
use Ignite\Element;

class GridView extends View {

	public function __construct($app, $viewID) {
		parent::__construct($app, $viewID);

		$this->elementsContainers['elements'] = $this->prependChild(new Element('es'));
		$this->elementsContainers['elements']->view = $this;
		$this->config = $this->prependChild(new ConfigContainer());
		$this->config->appendConfigSpec('Grid/config.json');
		$this->config['view_id'] = $viewID;
		$this->config['view_type'] = 'g';
		$this->config->view = $this;

		$this->parseConfiguration();
		$this->getElementsFromConfig();
	}

	/**
	 * @param string $key
	 * @param array|Element $content
	 * @return Element
	 * @throws \InvalidArgumentException
	 */
	public function addElement($key = null, $content = []) {
		$element = new Element('e', $content);

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

		if (isset($content['template_xml']) && strpos($content['template_xml'], 'http') !== 0) {
			$content['cpx'] = $this->app->getDispatchUrl().$content['template_xml'];
			unset($content['template_xml']);
		}

		if (isset($content['large_template_xml']) && strpos($content['large_template_xml'], 'http') !== 0) {
			$content['lpx'] = $this->app->getDispatchUrl().$content['large_template_xml'];
			unset($content['large_template_xml']);
		}

		$element->appendProperties($content);
		$element->view = $this;

		return $this->elementsContainers['elements']->appendChild($element);
	}

	/**
	 * Sets the view with that ID as the template for the grid.
	 * @param string $id
	 * @param null|string $key
	 */
	public function setTemplateView($id, $key = null) {
		if ($this->app->getView($id)['type'] !== 'wd')
			throw new \InvalidArgumentException("View with id '$id' is not a Widget View.");

		$this->config['template_xml'] = $this->app->getView($id)->getPath($key);
	}

	public function getPlaceholders() {
		$placeholders = [];

		/**
		 * @var $c Element
		 */
		foreach ($this->getIterator() as $c) {
			$placeholders = array_merge(array_keys($c->getProperties()));
		}

		return array_unique($placeholders);
	}

} 