<?php
namespace Ignite;

use Symfony\Component\Config\Definition\Processor;

abstract class View extends Registry {

	const ACTION_GROUP_SPEC				= 'action_group_elements.json';
	const LAUNCH_ACTIONS_SPEC			= 'launch_actions.json';
	const BUTTON_ELEMENTS_SPEC			= 'button_elements.json';
	const MENU_ELEMENTS_SPEC			= 'menu_elements.json';

	const GENERIC_ACTIONS_SPEC			= 'generic_actions.json';

	/**
	 * @var ElementContainer[]
	 */
	protected $elementsContainers = [];

	/**
	 * @var ConfigContainer
	 */
	protected $config = null;

	protected $actionsSpec = [];

	/**
	 * @var Application
	 */
	protected $app;
	protected $viewID;

	public $processor = null;

	public function __construct(Application $app) {
		parent::__construct('par');
		$this->processor = new Processor();
		$this->elementsContainers['action_groups'] = $this->appendChild(new ElementContainer(self::ACTION_GROUP_SPEC, 'ags'));
		$this->elementsContainers['action_groups']->view = $this;
		$this->elementsContainers['buttons'] = $this->appendChild(new ElementContainer(self::BUTTON_ELEMENTS_SPEC, 'bs'));
		$this->elementsContainers['buttons']->view = $this;
		$this->elementsContainers['launch_actions'] = $this->appendChild(new ElementContainer(self::LAUNCH_ACTIONS_SPEC, 'las'));
		$this->elementsContainers['launch_actions']->view = $this;
		$this->elementsContainers['visible_launch_actions'] = $this->appendChild(new ElementContainer(self::LAUNCH_ACTIONS_SPEC, 'vas'));
		$this->elementsContainers['visible_launch_actions']->view = $this;
		$this->elementsContainers['hidden_launch_actions'] = $this->appendChild(new ElementContainer(self::LAUNCH_ACTIONS_SPEC, 'has'));
		$this->elementsContainers['hidden_launch_actions']->view = $this;
		$this->elementsContainers['menus'] = $this->appendChild(new ElementContainer(self::MENU_ELEMENTS_SPEC, 'ms'));
		$this->elementsContainers['menus']->view = $this;

		$this->actionsSpec = json_decode(file_get_contents(ROOT_DIR.ConfigContainer::CONFIG_PATH.'/generic_actions.json'), true);

		$this->app = $app;
	}

	public function parseConfiguration($filepath) {
		$config = $this->app->scan($filepath)->getArray();
		$this->config->setProperties(array_merge($this->config->getProperties(), $config['cfg']));

		if (isset($config['landscape']))
			$this->config->addPrefixedProperties($config['landscape'], 'l');

		if (isset($config['tablet']))
			$this->config->addPrefixedProperties($config['tablet'], 'pad');

		if (isset($config['android']))
			$this->config->addPrefixedProperties($config['android'], 'and');

		if (isset($config['landscape_tablet']))
			$this->config->addPrefixedProperties($config['landscape_tablet'], 'padl');

		if (isset($config['landscape_android']))
			$this->config->addPrefixedProperties($config['landscape_android'], 'andl');

		if (isset($config['tablet_android']))
			$this->config->addPrefixedProperties($config['tablet_android'], 'andpad');

		if (isset($config['landscape_tablet_android']))
			$this->config->addPrefixedProperties($config['landscape_tablet_android'], 'andpadl');

	}

	public function addLaunchAction(Action $action, $type = null) {
		switch($type) {
			case Action::LAUNCH_ACTION_VISIBLE: {
				$wrapperTag = 'va';
				$where = 'visible_launch_actions';

				break;
			}
			case Action::LAUNCH_ACTION_HIDDEN: {
				$wrapperTag = 'ha';
				$where = 'hidden_launch_actions';

				break;
			}
			default: {
				$wrapperTag = 'la';
				$where = 'launch_actions';
			}
		}

		$action->setTag($wrapperTag);
		$this->elementsContainers[$where]->appendChild($action);
	}

	public function addMenu(Element $menu = null) {
		if ($menu === null)
			$menu = new Element('m');

		$menu->setTag('m');
		$this->elementsContainers['menus']->appendChild($menu);
		$menu->view = $this;

		return $menu;
	}

	/**
	 * @param array|Element|null $element
	 * @param Element $menu
	 * @return Element
	 */
	public function addMenuElement($element = null, Element $menu) {
		if ($element == null)
			$element = new Element('me');
		else if (is_array($element))
			$element = new Element('me', $element);

		$menu->appendChild($element);
		$element->view = $this;

		return $element;
	}

	/**
	 * @param Action[] $actions
	 * @param null|string $name
	 * @return int
	 */
	public function addActionGroup(array $actions, $name = null) {
		$actionGroup = new Element('ag');

		$this->elementsContainers['action_groups']->appendChild($actionGroup);

		$idx = count($this->elementsContainers['action_groups']);
		if (isset($name))
			$actionGroup['agn'] = $name;

		foreach ($actions as $a) {
			$a->setTag('age');
			$actionGroup->appendChild($a);
		}

		$actionGroup->view = $this;

		return $idx;
	}

	/**
	 * @param array|Element|null $group
	 * @return Element
	 */
	public function addButtonGroup($group = null) {
		if ($group == null)
			$group = new Element('bg');
		else if (!$group instanceof Element)
			$group = new Element('bg', $group);

		$group->setTag('bg');
		$group->view = $this;

		$this->elementsContainers['buttons']->appendChild($group);

		return $group;
	}

	/**
	 * @param array|Element $button
	 * @param Element|null $group
	 * @return Element
	 */
	public function addButtonElement($button, $group = null) {
		if (!$button instanceof Element)
			$button = new Element('b', $button);

		if ($group !== null) {
			$button->setTag('b');
			$group->appendChild($button);
		} else
			$this->elementsContainers['buttons']->appendChild($button);

		$button->view = $this;

		return $button;
	}

	public function validateAction(Action $a) {
		$name = $a->getName();

		return isset($this->actionsSpec[$name]);
	}

	public function render($update = false) {
		if ($this->render_cache !== [] && $update == false)
			return $this->render_cache;

		$result = [];

		foreach ($this->getChildren() as $c) {
			if ($c->isEmpty())
				continue;

			if ($c->getTag() !== null) {
				if (!isset($result[$c->getTag()]))
					$result[$c->getTag()] = [];

				$result[$c->getTag()][] = $c->render($update);
			}
			else
				$result = array_merge($result, $c->render($update));
		}

		if ($this->isRoot())
			$this->render_cache = [$this->tag => [$result]];
		else
			$this->render_cache = $result;

		return $this->render_cache;

	}

	/**
	 * @param string $k
	 * @param \Closure $v
	 */
	public function __set($k, $v) {
		$fresult = $v();

		if ($fresult instanceof Action)
			$fresult = [$fresult];

		if (is_array($fresult))
			$this->addActionGroup($fresult, $k);
	}

} 