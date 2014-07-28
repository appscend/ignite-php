<?php

namespace Ignite\Actions;

use Ignite\Action;

class DataStorage extends ActionBuffer {

	public static function store($data, $key, $form) {
		$action = new Action('s:k:gid:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function remove($key, $form) {
		$action = new Action('r:gid:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function removeAll($form) {
		$action = new Action('rgid:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function secureStore($data, $key) {
		$action = new Action('ss:k:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function removeSecure($key) {
		$action = new Action('rs:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

} 