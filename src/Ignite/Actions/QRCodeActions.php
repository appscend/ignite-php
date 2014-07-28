<?php

namespace Ignite\Actions;

use Ignite\Action;

class QRCodeActions extends ActionBuffer {

	public static function start() {
		$action = new Action('startListening');
		self::$actionBuffer[] = $action;

		return $action;
	}

} 