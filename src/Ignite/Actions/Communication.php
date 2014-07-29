<?php

namespace Ignite\Actions;

use Ignite\Action;

class Communication extends ActionBuffer{

	public static function call($number) {
		$action = new Action('call:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function email($address, $title = null, $body = null) {
		$actionName = 'e:';

		if ($title === null)
			$actionName .= 't:';
		if ($body === null)
			$actionName .= 'b:';

		$action = new Action($actionName, [$address, $title, $body]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function sms($number, $text = null) {
		$actionName = 'sms:';

		if ($text === null)
			$actionName .= 'b:';

		$action = new Action($actionName, [$number, $text]);
		self::$actionBuffer[] = $action;

		return $action;
	}

} 