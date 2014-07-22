<?php

namespace Ignite;

class Element extends Registry {

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

		if ($this->action !== null)
			$result = array_merge($result, $this->action->render());

		if ($this->isRoot())
			$this->render_cache = [$this->tag => [$result]];
		else
			$this->render_cache = $result;

		return $this->render_cache;
	}

	public function __set($k, $v) {
		if ($v instanceof \Closure) {
			$this->actionClosure = $v;
			$fresult = $v();

			if ($fresult instanceof Action) {
				$this->action = $fresult;

				return ;
			} else if (is_array($fresult)) {
				$this->action = new Action('pag:', [$k]);
				$this->view->addActionGroup($fresult, $k);
			}

		}
	}

}