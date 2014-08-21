<?php

namespace Ignite\Actions;

use Ignite\Action;

class ListActions extends ActionBuffer {

	/**
	 * @return Action
	 */
	public static function toggleSelectable() {
		$action = new Action('tsel');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $state
	 * @return Action
	 */
	public static function setSelectable($state) {
		$action = new Action('mpraa:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function toggleSelectUnique() {
		$action = new Action('tselu');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function executeActionsSelected() {
		$action = new Action('execsel');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function executeActionsSelectedAndToggle() {
		$action = new Action('execasel');
		self::$actionBuffer[] = $action;

		return $action;
	}
} 