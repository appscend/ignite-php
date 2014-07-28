<?php

namespace Ignite\Actions;

use Ignite\Action;

class System extends ActionBuffer{

	public static function callJSFunction($name, $params) {
		$action = new Action('func:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function alert($message) {
		$action = new Action('alert:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function removeAds($message) {
		$action = new Action('dropads:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function saveImage($url) {
		$action = new Action('si:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function openURL($url) {
		$action = new Action('url:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function mapDirections($lat, $long) {
		$action = new Action('dirl:l:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function addToFav() {
		$action = new Action('fav');
		self::$actionBuffer[] = $action;

		return $action;
	}
} 