<?php

namespace Ignite\Actions;

use Ignite\Action;

class Communication {

	public static function call($number) {
		return new Action('call:', func_get_args());
	}

	public static function email($address, $title = null, $body = null) {
		$actionName = 'e:';

		if (isset($title))
			$actionName .= 't:';
		if (isset($body))
			$actionName .= 'b:';

		return new Action($actionName, func_get_args());
	}

	public static function sms($number, $text = null) {
		$actionName = 'sms:';

		if (isset($title))
			$actionName .= 'b:';

		return new Action($actionName, func_get_args());
	}

} 