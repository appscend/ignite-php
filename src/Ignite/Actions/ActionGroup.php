<?php

namespace Ignite\Actions;

use Ignite\Action;

class ActionGroup extends ActionBuffer{

	public static function get($name, $prefix = '') {
		$a = new Action('pag:', [$name], $prefix);
		self::$actionBuffer[] = $a;

		return $a;
	}

} 