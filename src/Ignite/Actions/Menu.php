<?php

namespace Ignite\Actions;

use Ignite\Action;

class Menu extends ActionBuffer {

	public static function display($id) {
		$action = new Action('menu:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function close() {
		$action = new Action('cmenu');
		self::$actionBuffer[] = $action;

		return $action;
	}

} 