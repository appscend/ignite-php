<?php

namespace Ignite\Actions;

use Ignite\Action;

class MapActions {

	public static function showUserLocation() {
		return new Action('sloc');
	}

	public static function trackUserLocation() {
		return new Action('track');
	}

	public static function centerLocation() {
		return new Action('center');
	}

	public static function directionTo($name) {
		$actionName = 'dir';

		if ($name === '')
			$actionName .= ':';

		return new Action($actionName, func_get_args());
	}

	public static function locationSearch($name) {
		$actionName = 'locsearch';

		if ($name === '')
			$actionName .= ':';

		return new Action($actionName, func_get_args());
	}

} 