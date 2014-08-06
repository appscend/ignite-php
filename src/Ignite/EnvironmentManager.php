<?php

namespace Ignite;

use Silex\Application as SilexApp;
use Silex\ServiceProviderInterface;

class EnvironmentManager implements ServiceProviderInterface, \ArrayAccess {

	private $current = '';
	/**
	 * @var SilexApp
	 */
	private $app = null;

	private $envs = [];

	public function __construct() {
		$this->current = 'devel';
	}

	public function setEnvironment($name) {
		if (isset($this->envs[$name]))
			$this->current = $name;
		else
			return false;

		return true;
	}

	public function get($section) {
		$result = [];
		array_walk($this->envs[$this->current], function($v, $k) use ($section, &$result) {
			if (strpos($k, $section.'.') !== false)
				$result[$k] = $v;
		});

		return $result;
	}

	private function processConfig(array $arr) {
		$result = [];

		foreach($arr as $sectionName => $section) {
			foreach($section as $k => $v) {
				$result[$sectionName.'.'.$k] = $v;
			}
		}

		return $result;
	}

	/**
	 * Registers services on the given app.
	 *
	 * This method should only be used to configure services and parameters.
	 * It should not get services.
	 *
	 * @param SilexApp $app An Application instance
	 */
	public function register(SilexApp $app) {
		$this->app = $app;
		$app['env'] = $this;
		$this->envs['devel'] = $this->processConfig($this->app->scan(ROOT_DIR.'/config/devel.toml')->getArray());
		$this->envs['production'] = $this->processConfig($this->app->scan(ROOT_DIR.'/config/production.toml')->getArray());
	}

	/**
	 * Bootstraps the application.
	 *
	 * This method is called after all services are registered
	 * and should be used for "dynamic" configuration (whenever
	 * a service must be requested).
	 */
	public function boot(SilexApp $app) {

	}

	/**
	 * Whether a offset exists
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 * @param mixed $offset
	 * An offset to check for.
	 * @return boolean true on success or false on failure.
	 */
	public function offsetExists($offset) {
		return array_key_exists($offset, $this->envs[$this->current]);
	}

	/**
	 * Offset to retrieve
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 * @param mixed $offset
	 * The offset to retrieve.
	 * @return mixed Can return all value types.
	 */
	public function offsetGet($offset) {
		return $this->envs[$this->current][$offset];
	}

	/**
	 * Offset to set
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 * @param mixed $offset
	 * @param mixed $value
	 * @return void
	 */
	public function offsetSet($offset, $value) {
		return null;
	}

	/**
	 * Offset to unset
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 * @param mixed $offset
	 * @return void
	 */
	public function offsetUnset($offset) {
		return null;
	}
}