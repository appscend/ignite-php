<?php

namespace Ignite\Actions;

use Ignite\Action;

class QRCodeActions extends ActionBuffer {

	/**
	 * @return Action
	 */
	public static function start() {
		$action = new Action('startListening');
		self::$actionBuffer[] = $action;

		return $action;
	}

} 