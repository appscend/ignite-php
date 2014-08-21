<?php

namespace Ignite\Actions;

use Ignite\Action;

class SocialNetwork extends ActionBuffer {

	/**
	 * @param string $title
	 * @param string $text
	 * @param string $url
	 * @param string $image
	 * @param string $service
	 * @return Action
	 */
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