<?php

namespace Ignite\Actions;

use Ignite\Action;

class DataStorage extends ActionBuffer {

	/**
	 * @param string $data
	 * @param string $key
	 * @param string $form
	 * @return Action
	 */
	public static function store($data, $key, $form) {
		$action = new Action('s:k:gid:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $key
	 * @param string $form
	 * @return Action
	 */
	public static function remove($key, $form) {
		$action = new Action('r:gid:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $form
	 * @return Action
	 */
	public static function removeAll($form) {
		$action = new Action('rgid:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $data
	 * @param string $key
	 * @return Action
	 */
	public static function secureStore($data, $key) {
		$action = new Action('ss:k:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $key
	 * @return Action
	 */
	public static function removeSecure($key) {
		$action = new Action('rs:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

} 