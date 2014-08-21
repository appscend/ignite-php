<?php

namespace Ignite\Actions;

use Ignite\Action;

class MapActions extends ActionBuffer {

	/**
	 * @return Action
	 */
	public static function showUserLocation() {
		$action = new Action('sloc');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function trackUserLocation() {
		$action = new Action('track');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function centerLocation() {
		$action = new Action('center');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $name
	 * @return Action
	 */
	public static function directionTo($name = null) {
		$actionName = 'dir';

		if ($name !== null)
			$actionName .= ':';

		$action = new Action($actionName, [$name]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $name
	 * @return Action
	 */
	public static function locationSearch($name = null) {
		$actionName = 'locsearch';

		if ($name !== null)
			$actionName .= ':';

		$action = new Action($actionName, [$name]);
		self::$actionBuffer[] = $action;

		return $action;
	}

} 