<?php

namespace Ignite;

use Ignite\Actions\ActionBuffer;
use Ignite\Actions\ActionGroup;
use Symfony\Component\HttpFoundation\Request;

class ViewStub implements \ArrayAccess {

	private $viewTypes = [
		'cam',
		'g',
		'fr',
		'lr',
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

		return $c($this->app, new Request($args));
	}

	public function getPath($key = null) {
		if (!$this->isStatic)
			return $this->app->getWebPath().$this->properties['route'];

		return $this->app->getStaticXMLPath().$this->app->getCurrentModule()->getName()."/{$this->properties['id']}.$key.xml";
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

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Whether a offset exists
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 * @param mixed $offset <p>
	 * An offset to check for.
	 * </p>
	 * @return boolean true on success or false on failure.
	 * </p>
	 * <p>
	 * The return value will be casted to boolean if non-boolean was returned.
	 */
	public function offsetExists($offset) {
		return array_key_exists($offset, $this->properties);
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to retrieve
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 * @param mixed $offset <p>
	 * The offset to retrieve.
	 * </p>
	 * @return mixed Can return all value types.
	 */
	public function offsetGet($offset) {
		return $this->properties[$offset];
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to set
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 * @param mixed $offset <p>
	 * The offset to assign the value to.
	 * </p>
	 * @param mixed $value <p>
	 * The value to set.
	 * </p>
	 * @return void
	 */
	public function offsetSet($offset, $value) {
		return ;
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to unset
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 * @param mixed $offset <p>
	 * The offset to unset.
	 * </p>
	 * @return void
	 */
	public function offsetUnset($offset) {
		return ;
	}
}