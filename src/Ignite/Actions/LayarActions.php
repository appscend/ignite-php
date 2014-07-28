<?php

namespace Ignite\Actions;

use Ignite\Action;

class LayarActions extends ActionBuffer {

	public static function start() {
		$action = new Action('startListening');
		self::$actionBuffer[] = $action;

		return $action;
	}

} 