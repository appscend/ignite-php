<?php

namespace Ignite\Actions;

use Ignite\Action;

class Communication {

	public static function call($number) {
		return new Action('call:', func_get_args());
	}

	public static function email($address, $title, $body) {
		$actionName = 'e:';

		if ($title === '')
			$actionName .= 't:';
		if ($body === '')
			$actionName .= 'b:';

		return new Action($actionName, func_get_args());
	}

	public static function sms($number, $text) {
		$actionName = 'sms:';

		if ($text === '')
			$actionName .= 'b:';

		return new Action($actionName, func_get_args());
	}

} 