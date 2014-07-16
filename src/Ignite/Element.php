<?php

namespace Ignite;

class Element extends Registry{

	/**
	 * @var Element[]
	 */
	protected $children = [];

	/**
	 * @var Action
	 */
	protected $action	= null;

	/**
	 * @var \Closure[]
	 */
	private $actionClosure = [];

	public function __construct($vars = [], $wrapperTag = null) {
		parent::__construct($wrapperTag);
		$this->_vars = $vars;
	}

	/**
	 * @param Element|Action $child
	 */
	public function addChild($child) {
		if (!isset($this->children[$child->wrapperTag]))
			$this->children[$child->wrapperTag] = [];

		$this->children[$child->wrapperTag][] = $child;
	}

	public function setView($v) {
		if ($v instanceof View)
			$this->view = $v;
		else {
			throw new \InvalidArgumentException("Argument 1 must be an instance of \\Ignite\\View");

			return false;
		}

		return true;
	}

	public function setAction(Action $a) {
		$this->action = $a;
	}

	public function render() {
		if (empty($this->actionClosure))
			return parent::render();

		/**
		 * @var $cl \Closure
		 */
		$cl = current($this->actionClosure);
		$name = key($this->actionClosure);
		$fresult = $cl();


		if ($fresult instanceof Action) {
			$this->action = $fresult;
		} else if (is_array($fresult)) {

			if (is_string($name)) {
				$index = $name;
				$this->view->addActionGroup($fresult, $index);
			} else
				$index = $this->view->addActionGroup($fresult);

			$this->action = new Action('pag:', [$index]);
		}

		$result = [];

		if ($this->action !== null) {
			$renderedAction = $this->action->render();
			if ($renderedAction !== null)
				$result = array_merge($result, $renderedAction);
		}

		return array_merge($result, parent::render());
	}

	public function __set($key, $value) {
		if ($value instanceof \Closure)
			$this->actionClosure[$key] = $value;
		else
			parent::__set($key, $value);
	}

}
