<?php
namespace Ignite\Actions;

use Ignite\Action;

class LocalPushNotifications extends ActionBuffer {

	public static function scheduleAt($text, $date, $before) {
		$action = new Action('spn:d:b:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function schedule($before) {
		$actionName = 'spn';

		if ($before === '')
			$actionName .= ':';

		$action = new Action($actionName, func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function remove($id) {
		$action = new Action('rpn:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

} 