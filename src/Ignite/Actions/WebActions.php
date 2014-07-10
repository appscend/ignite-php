<?php

namespace Ignite\Actions;

use Ignite\Action;

class WebActions {

	public static function refresh() {
		return new Action('r');
	}

	public static function back() {
		return new Action('b');
	}

	public static function forward() {
		return new Action('f');
	}

	public static function bookmark() {
		return new Action('fav');
	}

	public static function increaseFontSize() {
		return new Action('fp');
	}

	public static function decreaseFontSize() {
		return new Action('fm');
	}

	public static function cycleFontSizes() {
		return new Action('fa');
	}

	public static function sharePage($service) {
		$actionName = 'share';

		if (isset($service))
			$actionName .= ':';

		return new Action($actionName, func_get_args());
	}

	public static function loadNextItem() {
		return new Action('ni');
	}

	public static function loadPreviousItem() {
		return new Action('pi');
	}

	public static function getInfoEvent() {
		return new Action('getinfo');
	}

	public static function callJSFunction($fname) {
		return new Action('trigger:', func_get_args());
	}

	public static function getFormEvent($id) {
		return new Action('getform:', func_get_args());
	}

	public static function getDataEvent($url) {
		return new Action('getdata:', func_get_args());
	}
} 