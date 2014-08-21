<?php

namespace Ignite\Actions;

use Ignite\Action;

class CameraActions extends ActionBuffer{

	/**
	 * @return Action
	 */
	public static function swapCamera() {
		$action = new Action('swap');
		self::$actionBuffer[] = $action;

		return $action;
	}

} 