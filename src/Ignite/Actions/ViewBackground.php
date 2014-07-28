<?php

namespace Ignite\Actions;

use Ignite\Action;

class ViewBackground extends ActionBuffer {

	public static function setBackgroundImage($image, $duration) {
		$actionName = 'sbi:';

		if ($duration === '')
			$actionName .= 'd:';

		$action = new Action($actionName, func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function setBackgroundVideo($video) {
		$action = new Action('sbv:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function setBackgroundColor($color, $duration) {
		$actionName = 'sbc:';

		if ($duration === '')
			$actionName .= 'd:';

		$action = new Action($actionName, func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

} 