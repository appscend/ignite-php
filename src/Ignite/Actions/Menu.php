<?php

namespace Ignite\Actions;

use Ignite\Action;

class Menu {

	public static function display($id) {
		return new Action('menu:', func_get_args());
	}

	public static function close() {
		return new Action('cmenu');
	}

} 