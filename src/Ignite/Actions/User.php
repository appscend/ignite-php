<?php

namespace Ignite\Actions;

use Ignite\Action;

class User extends ActionBuffer {

	public static function login() {
		$action = new Action('login');
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function loginAdapter($adapter) {
		$action = new Action('li:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function logoutAdapter($adapter) {
		$action = new Action('lo:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function toggleLogin($adapter) {
		$action = new Action('tl:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function addPersistentData($data, $key) {
		$action = new Action('sud:k:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function removePersistentData($key) {
		$action = new Action('rudk:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

} 