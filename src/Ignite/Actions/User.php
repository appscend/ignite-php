<?php

namespace Ignite\Actions;

use Ignite\Action;

class User {

	public static function login() {
		return new Action('login');
	}

	public static function loginAdapter($adapter) {
		return new Action('li:', func_get_args());
	}

	public static function logoutAdapter($adapter) {
		return new Action('lo:', func_get_args());
	}

	public static function toggleLogin($adapter) {
		return new Action('tl:', func_get_args());
	}

	public static function addPersistentData($data, $key) {
		return new Action('sud:k:', func_get_args());
	}

	public static function removePersistentData($key) {
		return new Action('rudk:', func_get_args());
	}

} 