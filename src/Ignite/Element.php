<?php

namespace Ignite;

use Symfony\Component\Config\Definition\Exception\InvalidTypeException;

class Element extends Registry {

	const FOR_LANDSCAPE = 1;
	const FOR_TABLET	= 2;
	const FOR_ANDROID	= 4;

	/**
	 * @var Action
	 */
	protected $action 			= null;
	protected $actionClosure 	= null;

	/**
	 * @var View
	 */
	public $view = null;

	public function __construct($tag = null, array $properties = []) {
		$this->tag = $tag;
		$this->properties = $properties;
		for ($i=0; $i < 7; $i++) {
			$this->prefix_properties[$i] = [];
		}
	}

	/**
	 * @param \Closure|Action
	 * @return $this
	 */
	public function onTap($action) {
		if ($action instanceof \Closure) {
			$fresult = $action();

			if ($fresult instanceof Action) {
				$this->action = $fresult;

			} else if (is_array($fresult)) {
				$index = $this->view->addActionGroup($fresult);
				$this->action = new Action('pag:', [$index-1]);
			}
		} else
			$this->action = $action;

		return $this;
	}

	/**
	 * @param array $props
	 * @param int $where
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function setFor(array $props, $where) {
		if (1 > $where || 7 < $where)
			throw new \InvalidArgumentException("Invalid prefix mask for element.");

		$this->prefix_properties[$where-1] = array_merge($this->prefix_properties[$where-1], $props);

		return $this;
	}

	public function getFor($what) {
		return $this->prefix_properties[$what];
	}

	public function render($update = false) {
		if ($this->render_cache !== [] && $update == false)
			return $this->render_cache;

		$result = [];

		foreach ($this->properties as $name => $prop) {
			$result[$name] = $prop;
		}

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

		if ($this->action !== null) {
			if ($this->view !== null && !$this->view->validateAction($this->action)) {
				throw new InvalidTypeException("Action '{$this->action->getName()}' is not a valid action.");
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