<?php

namespace Ignite\Actions;

use Ignite\Action;

class CameraActions extends ActionBuffer{

	public static function swapCamera() {
		$action = new Action('swap');
		self::$actionBuffer[] = $action;

		return $action;
	}

} 