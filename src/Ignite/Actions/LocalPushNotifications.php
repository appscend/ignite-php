<?php
namespace Ignite\Actions;

use Ignite\Action;

class LocalPushNotifications {

	public static function scheduleAt($text, $date, $before) {
		return new Action('spn:d:b:', func_get_args());
	}

	public static function schedule($before) {
		$actionName = 'spn';

		if ($before === '')
			$actionName .= ':';

		return new Action($actionName, func_get_args());
	}

	public static function remove($id) {
		return new Action('rpn:', func_get_args());
	}

} 