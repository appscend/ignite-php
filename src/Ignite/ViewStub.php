<?php

namespace Ignite;

use Ignite\Actions\ActionBuffer;
use Ignite\Actions\ActionGroup;

class ViewStub {

	private $viewTypes = [
		'cam',
		'c',
		'fr',
		'p',
		'lr',
		'l',
		'm',
		'mb',
		'cs',
		't',
		'w',
		'wd',
	];

	private $isStatic = false;
	private $properties = [];
	/**
	 * @var \Closure
	 */
	private $viewClosure = null;
	/**
	 * @var Application
	 */
	private $app = null;

	public function __construct($id, $type, $r, \Closure $f) {
		$this->properties['id'] = $id;
		$this->properties['type'] = $this->viewTypes[$type];
		$this->properties['route'] = $r;

		$this->viewClosure = $f;
	}

	/**
	 * @param array $args
	 * @return View
	 */
	public function getFullView($args = []) {
		$c = $this->viewClosure;

		return $c($this->app, $args);
	}

	public function getPath() {
		if (!$this->isStatic)
			return $this->properties['route'];

		return $this->app->getStaticXMLPath().'/'.$this->app->getCurrentModule()->getName().'/'.$this->properties['id'].'.xml';
	}

	public function setApp(Application $app) {
		$this->app = $app;
	}

	public function setStatic($s) {
		$this->isStatic = boolval($s);
	}

	public function getApp() {
		return $this->app;
	}

	public function __get($k) {
		if (isset($this->properties[$k]))
			return $this->properties[$k];

		return null;
	}

	/**
	 *
	 * These metods are used to generate an action group executed with this view's ID (tavi)
	 *
	 * @param $name
	 * @param $params
	 */
	public function __call($name, $params) {
		$a = ActionGroup::get($name)->on($this->properties['id']);
	}

} 