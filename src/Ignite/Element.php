<?php

namespace Ignite;

use Ignite\Actions\ActionBuffer;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;

class Element extends Registry {

	/**
	 *	Mask used for properties available only in landscape mode.
	 */
	const FOR_LANDSCAPE = 1;
	/**
	 *	Mask used for properties available only for tablets.
	 */
	const FOR_TABLET	= 2;
	/**
	 * Mask used for properties available only for android.
	 */
	const FOR_ANDROID	= 4;

	/**
	 * @var Action Element action
	 */
	protected $action 			= null;
	/**
	 * @var \Closure the closure from which the Action originated
	 */
	protected $actionClosure 	= null;

	/**
	 * @var View The view instance where this element belongs to.
	 */
	public $view = null;

	/**
	 *
	 * Creates a new element
	 *
	 * @param string $tag
	 * @param array $properties
	 */
	public function __construct($tag = null, array $properties = []) {
		$this->tag = $tag;
		$this->properties = $properties;
		$this->prefix_properties = array_fill(0, 7, []);
	}

	/**
	 *
	 * Action to perform on tap.
	 *
	 * @param \Closure|Action $action Closure which contains calls to the static Action classes or just one action.
	 * @param string $name In case multiple actions are present, specificy a name for the action group. Defaults to
	 * the index of the group after insertion.
	 * @return $this This instance useful for chaining methods.
	 */
	public function onTap($action, $name = null) {
		if ($action instanceof \Closure) {
			$action();
			$fresult = ActionBuffer::getBuffer();

			if (!isset($fresult[1])) {
				$this->action = $fresult[0];

			} else {

				$el = $this->view->addActionGroup($fresult, $name);

				if ($name !== null)
					$this->action = new Action('pag:', [$name]);
				else {
					$index = $this->view['action_groups']->getChildIndex($el);
					$this->action = new Action('pag:', [$index]);
				}
			}
		} else if ($action instanceof Action)
			$this->action = $action;

		return $this;
	}

	/**
	 *
	 * Sets properties for specific platforms/modes.
	 *
	 * @param array $props The properties
	 * @param integer $where A mask containing the platform/mode combination for which the properties are available
	 * @return $this This instance useful for chaining methods.
	 * @throws \InvalidArgumentException If the mask is invalid.
	 */
	public function setFor(array $props, $where) {
		if (1 > $where || 7 < $where)
			throw new \InvalidArgumentException("Invalid prefix mask '$where' for element '{$this->getTag()}' in view '{$this->view->getID()}' .");

		$this->prefix_properties[$where-1] = array_merge($this->prefix_properties[$where-1], $props);

		return $this;
	}

	/**
	 *
	 * Sets properties for specific platforms/modes.
	 *
	 * @param integer $what A mask containing the platform/mode combination for which the properties are available
	 * @return array
	 */
	public function getFor($what) {
		return $this->prefix_properties[$what-1];
	}

	/**
	 * @param bool $update
	 * @return array
	 * @throws InvalidTypeException If the Action contained in this Element is not valid
	 */
	public function render($update = false) {
		if ($this->render_cache !== [] && $update == false)
			return $this->render_cache;

		$result = [];

		foreach ($this->properties as $name => $prop) {
			$result[$name] = $prop;
		}

		/**
		 * @var Registry $c
		 */
		foreach ($this->getIterator() as $c) {
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

		if ($this->action !== null) {
			if ($this->view !== null && !$this->view->validateAction($this->action)) {
				throw new InvalidTypeException("Action '{$this->action->getName()}' is not a valid action for element '{$this->getTag()}' in view '{$this->view->getID()}' .");
			} else {
				$result = array_merge($result, $this->action->render());
			}
		}

		if ($this->isRoot())
			$this->render_cache = [$this->tag => [$result]];
		else
			$this->render_cache = $result;

		return $this->render_cache;
	}

}