<?php

namespace Ignite\Views;
use Ignite\ConfigContainer;
use Ignite\ElementContainer;
use Ignite\View;
use Ignite\Element;
use Ignite\Action;
use Ignite\Actions\ActionBuffer;

class ImageGridView extends View {

	const ELEMENTS_CONFIG_SPEC_FILE = 'ImageGrid/elements.json';
	const ACTIONS_CONFIG_SPEC_FILE = 'ImageGrid/actions.json';

	private $paramsElemPath = ['image'];

	public function __construct($app, $viewID) {
		parent::__construct($app, $viewID);

		$this->elementsContainers['elements'] = $this->prependChild(new ElementContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 'es'));
		$this->elementsContainers['elements']->view = $this;
		$this->config = $this->prependChild(new ConfigContainer());
		$this->config->appendConfigSpec('ImageGrid/config.json');
		$this->config['view_id'] = $viewID;
		$this->config['view_type'] = 'p';
		$this->config->view = $this;

		array_push($this->pathParameters, 'placeholder_image_path');

		$this->actionsSpec = array_merge($this->actionsSpec, json_decode(file_get_contents(LIB_ROOT_DIR.ConfigContainer::CONFIG_PATH.'/'.self::ACTIONS_CONFIG_SPEC_FILE), true));
		$this->parseConfiguration();
		$this->getElementsFromConfig();
	}

	/**
	 * @param array|Element $content
	 * @return int
	 */
	public function addImage($key = null, $content = []) {
		if (!empty($content))
			$this->processAssetsPaths($content, $this->paramsElemPath);
		$content = new Element('e', $content);

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
		if (strpos($content['image'], 'http') !== 0)
			$content['image'] = $this->app->getAssetsPath().$content['image'];

		return $this->elementsContainers['elements']->appendChild($content);
	}

	public function getImage($idx) {
		return $this->elementsContainers['elements']->getChild($idx);
	}

	public function removeImage($idx) {
		return $this->elementsContainers['elements']->removeChild($idx);
	}

	public function getImages() {
		return $this->elementsContainers['elements']->getChildren();
	}

	/**
	 * @param \Closure|Action $action
	 * @param string $name
	 * @throws \InvalidArgumentException
	 */
	public function onPageFlip($action, $name = null) {
		if ($action instanceof \Closure) {
			$action();

			$fresult = ActionBuffer::getAndClearBuffer();

			if (!isset($fresult[1])) {
				$ac = $fresult[0];
				$ac->setPrefix('pg');
			} else {

				$el = $this->addActionGroup($fresult, $name);

				if ($name !== null)
					$ac = new Action('pag:', [$name], 'pg');
				else {
					$index = $this['action_groups']->getChildIndex($el);
					$ac = new Action('pag:', [$index-1], 'pg');
				}
			}
		} else if ($action instanceof Action) {
			$ac = $action;
			$ac->setPrefix('pg');
		} else
			throw new \InvalidArgumentException("Parameter 1 for 'onPageFlip' must be instance of Action or Closure.");

		$this->config->appendChild($ac);
	}

} 