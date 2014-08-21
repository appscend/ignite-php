<?php

namespace Ignite\Actions;

use Ignite\Action;

class User extends ActionBuffer {

	/**
	 * @return Action
	 */
	public static function login() {
		$action = new Action('login');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string  $adapter
	 * @return Action
	 */
	public static function loginAdapter($adapter) {
		$action = new Action('li:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string  $adapter
	 * @return Action
	 */
	public static function logoutAdapter($adapter) {
		$action = new Action('lo:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $adapter
	 * @return Action
	 */
	public static function toggleLogin($adapter) {
		$action = new Action('tl:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $data
	 * @param string $key
	 * @return Action
	 */
	public static function addPersistentData($data, $key) {
		$action = new Action('sud:k:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $key
	 * @return Action
	 */
	public static function removePersistentData($key) {
		$action = new Action('rudk:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

} 