<?php

namespace Ignite\Actions;

use Ignite\Action;

class Menu extends ActionBuffer {

	/**
	 * @param string|integer $id
	 * @return Action
	 */
	public static function display($id) {
		$action = new Action('menu:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function close() {
		$action = new Action('cmenu');
		self::$actionBuffer[] = $action;

		return $action;
	}

} 