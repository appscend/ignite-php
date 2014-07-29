<?php

namespace Ignite\Actions;

use Ignite\Action;

class ViewBackground extends ActionBuffer {

	public static function setBackgroundImage($image, $duration = null) {
		$actionName = 'sbi:';

		if ($duration === null)
			$actionName .= 'd:';

		$action = new Action($actionName, [$image, $duration]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function setBackgroundVideo($video) {
		$action = new Action('sbv:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function setBackgroundColor($color, $duration = null) {
		$actionName = 'sbc:';

		if ($duration === null)
			$actionName .= 'd:';

		$action = new Action($actionName, [$color, $duration]);
		self::$actionBuffer[] = $action;

		return $action;
	}

} 