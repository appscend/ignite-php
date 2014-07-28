<?php

namespace Ignite\Actions;

use Ignite\Action;

class SocialNetwork extends ActionBuffer {

	public static function share($title, $text, $url, $image, $service) {
		$actionName = 'share:t:u:';

		if ($image === '')
			$actionName .= 'i:';
		if ($service === '')
			$actionName .= 's:';

		$action = new Action($actionName, func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

} 