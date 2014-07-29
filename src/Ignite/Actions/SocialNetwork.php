<?php

namespace Ignite\Actions;

use Ignite\Action;

class SocialNetwork extends ActionBuffer {

	public static function share($title, $text, $url, $image = null, $service = null) {
		$actionName = 'share:t:u:';

		if ($image === null)
			$actionName .= 'i:';
		if ($service === null)
			$actionName .= 's:';

		$action = new Action($actionName, [$title, $text, $url, $image, $service]);
		self::$actionBuffer[] = $action;

		return $action;
	}

} 