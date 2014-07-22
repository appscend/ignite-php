<?php
namespace Ignite;

class View extends Registry {

	const ACTION_GROUP_SPEC				= 'action_group_elements.json';
	const LAUNCH_ACTIONS_SPEC			= 'launch_actions.json';
	const BUTTON_ELEMENTS_SPEC			= 'button_elements.json';
	const MENU_ELEMENTS_SPEC			= 'menu_elements.json';

	/**
	 * @var ElementContainer[]
	 */
	protected $elementsContainers = [];

	/**
	 * @var ConfigContainer
	 */
	protected $config = null;

	/**
	 * @var Application
	 */
	protected $app;
	protected $viewID;

	public function __construct(Application $app) {
		parent::__construct('par');
		$this->elementsContainers['action_groups'] = $this->appendChild(new ElementContainer(self::ACTION_GROUP_SPEC, 'ags'));
		$this->elementsContainers['buttons'] = $this->appendChild(new ElementContainer(self::BUTTON_ELEMENTS_SPEC, 'bs'));
		$this->elementsContainers['launch_actions'] = $this->appendChild(new ElementContainer(self::LAUNCH_ACTIONS_SPEC, 'las'));
		$this->elementsContainers['visible_launch_actions'] = $this->appendChild(new ElementContainer(self::LAUNCH_ACTIONS_SPEC, 'vas'));
		$this->elementsContainers['hidden_launch_actions'] = $this->appendChild(new ElementContainer(self::LAUNCH_ACTIONS_SPEC, 'has'));
		$this->elementsContainers['menus'] = $this->appendChild(new ElementContainer(self::MENU_ELEMENTS_SPEC, 'ms'));

		$this->app = $app;
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

		return $button;
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

} 