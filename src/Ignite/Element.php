<?php

namespace Ignite;

class Element extends Registry{

	/**
	 * @var Action
	 */
	protected $action	= null;

	/**
	 * @var array(\Closure)
	 */
	private $actionClosure = [];

	public function __construct($vars, $wrapperTag = null) {
		parent::__construct($wrapperTag);
		$this->_vars = $vars;
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

	public function render() {
		if (empty($this->actionClosure))
			return parent::render();

		/**
		 * @var $cl \Closure
		 */
		$cl = current($this->actionClosure);
		$fresult = $cl();


		if ($fresult instanceof Action) {
			$this->action = $fresult;
		} else if (is_array($fresult)) {
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
