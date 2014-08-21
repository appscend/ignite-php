<?php

namespace Ignite\Actions;

use Ignite\Action;

class LayarActions extends ActionBuffer {

	/**
	 * @return Action
	 */
	public static function start() {
		$action = new Action('startListening');
		self::$actionBuffer[] = $action;

		return $action;
	}

} 