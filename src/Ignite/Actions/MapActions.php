<?php

namespace Ignite\Actions;

use Ignite\Action;

class MapActions extends ActionBuffer {

	public static function showUserLocation() {
		$action = new Action('sloc');
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function trackUserLocation() {
		$action = new Action('track');
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function centerLocation() {
		$action = new Action('center');
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function directionTo($name) {
		$actionName = 'dir';

		if ($name === '')
			$actionName .= ':';

		$action = new Action($actionName, func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function locationSearch($name) {
		$actionName = 'locsearch';

		if ($name === '')
			$actionName .= ':';

		$action = new Action($actionName, func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

} 