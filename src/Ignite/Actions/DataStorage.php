<?php

namespace Ignite\Actions;

use Ignite\Action;

class DataStorage {

	public static function setBackgroundImage($image, $duration) {
		$actionName = 'sbi:';

		if ($duration === '')
			$actionName .= 'd:';

		return new Action($actionName, func_get_args());
	}

	public static function setBackgroundVideo($video) {
		return new Action('sbv:', func_get_args());
	}

	public static function setBackgroundColor($color, $duration) {
		$actionName = 'sbc:';

		if ($duration === '')
			$actionName .= 'd:';

		return new Action($actionName, func_get_args());
	}

} 