<?php

namespace Ignite\Actions;

use Ignite\Action;

class SocialNetwork {

	public static function share($title, $text, $url, $image = null, $service = null) {
		$actionName = 'share:t:u:';

		if (isset($image))
			$actionName .= 'i:';
		if (isset($service))
			$actionName .= 's:';

		return new Action($actionName, func_get_args());
	}

} 