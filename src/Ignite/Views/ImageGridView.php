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

	public function __construct($app, $viewID) {
		parent::__construct($app);
		$this->viewID = $viewID;
		$this->elementsContainers['elements'] = $this->prependChild(new ElementContainer(self::ELEMENTS_CONFIG_SPEC_FILE, 'es'));
		$this->elementsContainers['elements']->view = $this;
		$this->config = $this->prependChild(new ConfigContainer());
		$this->config->appendConfigSpec('ImageGrid/config.json');
		$this->config['view_id'] = $viewID;
		$this->config['view_type'] = 'p';
		$this->config->view = $this;

		$this->actionsSpec = array_merge($this->actionsSpec, json_decode(file_get_contents(ROOT_DIR.ConfigContainer::CONFIG_PATH.'/'.self::ACTIONS_CONFIG_SPEC_FILE), true));
	}

	/**
	 * @param array|Element $content
	 * @return int
	 */
	public function addImage($content) {
		if ($content instanceof Element) {
			$content->view = $this;
			$this->elementsContainers['elements']->appendChild($content);
		} else {
			$el = new Element('e', $content);
			$el->view = $this;
			$this->elementsContainers['elements']->appendChild($el);
		}

		return count($this->elementsContainers['elements'])-1;
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