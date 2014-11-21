<?php

namespace Ignite;

define("APP_ROOT_DIR", realpath('.'));
define("LIB_ROOT_DIR", APP_ROOT_DIR.'/vendor/appscend/ignite-php');
define("CONFIG_DIR", APP_ROOT_DIR.'/config');

use Silex\Application as SilexApp;
use Silex\ServiceProviderInterface;
use Symfony\Component\Config\Definition\Processor;
use Yosymfony\Toml\Toml;

class EnvironmentManager implements ServiceProviderInterface, \ArrayAccess {

	/**
	 * @var string The current environment.
	 */
	private static $current;
	/**
	 * @var SilexApp Application instance which uses this manager
	 */
	private static $app = null;

	/**
	 * @var array The environments
	 */
	private static $envs = [];

	/**
	 *
	 * Sets an environment as the current one.
	 *
	 * @param string $name
	 * @return bool True if the environment exists, false otherwise.
	 */
	public static function setEnvironment($name) {
		if (!file_exists(CONFIG_DIR.'/environments/'.$name.'.toml'))
			throw new \InvalidArgumentException("Configuration file for environment '$name' doesn't exist.");

		self::$envs[$name] = self::processConfig(Toml::parse(CONFIG_DIR."/environments/$name.toml"));
		self::$current = $name;
	}

	public static function init($environment = 'devel') {
		if (self::$current !== null)
			return;

		self::setEnvironment($environment);
	}

	/**
	 *
	 * Returns the properties of a specific section
	 *
	 * @param string $section Section name
	 * @return array
	 */
	public static function get($section) {
		$result = [];
		array_walk(self::$envs[self::$current], function($v, $k) use ($section, &$result) {
			if (strpos($k, $section.'.') !== false)
				$result[$k] = $v;
		});

		return $result;
	}

	/**
	 *
	 * Processes the parsed configuration in the form [section.property] = value
	 *
	 * @param array $arr
	 * @return array
	 */
	private static function processConfig(array $arr) {
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
		self::$app = $app;
		$app['env'] = $this;
		self::$envs['devel'] = self::processConfig(Toml::parse(CONFIG_DIR.'/environments/devel.toml'));
		self::$envs['production'] = self::processConfig(Toml::parse(CONFIG_DIR.'/environments/production.toml'));
	}

	public static function app() {
		return self::$app;
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
		return array_key_exists($offset, self::$envs[self::$current]);
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
		return self::$envs[self::$current][$offset];
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