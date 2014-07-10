<?php

namespace Ignite\Actions;

use Ignite\Action;

class SocialNetwork {

	public static function share($title, $text, $url, $image, $service) {
		$actionName = 'share:t:u:';

		if ($image === '')
			$actionName .= 'i:';
		if ($service === '')
			$actionName .= 's:';

		return new Action($actionName, func_get_args());
	}

} 