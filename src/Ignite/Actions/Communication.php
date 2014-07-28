<?php

namespace Ignite\Actions;

use Ignite\Action;

class Communication extends ActionBuffer{

	public static function call($number) {
		$action = new Action('call:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function email($address, $title, $body) {
		$actionName = 'e:';

		if ($title === '')
			$actionName .= 't:';
		if ($body === '')
			$actionName .= 'b:';

		$action = new Action($actionName, func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function sms($number, $text) {
		$actionName = 'sms:';

		if ($text === '')
			$actionName .= 'b:';

		$action = new Action($actionName, func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

} 