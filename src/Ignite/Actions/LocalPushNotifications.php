<?php
namespace Ignite\Actions;

use Ignite\Action;

class LocalPushNotifications extends ActionBuffer {

	/**
	 * @param string $text
	 * @param string $date
	 * @param string $before
	 * @return Action
	 */
	public static function scheduleAt($text, $date, $before) {
		$action = new Action('spn:d:b:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $before
	 * @return Action
	 */
	public static function schedule($before = null) {
		$actionName = 'spn';

		if ($before === null)
			$actionName .= ':';

		$action = new Action($actionName, [$before]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $id
	 * @return Action
	 */
	public static function remove($id) {
		$action = new Action('rpn:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

} 