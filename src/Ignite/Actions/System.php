<?php

namespace Ignite\Actions;

use Ignite\Action;

class System {

	public static function callJSFunction($name, $params) {
		return new Action('func:', func_get_args());
	}

	public static function alert($message) {
		return new Action('alert:', func_get_args());
	}

	public static function removeAds($message) {
		return new Action('dropads:', func_get_args());
	}

	public static function saveImage($url) {
		return new Action('si:', func_get_args());
	}

	public static function openURL($url) {
		return new Action('url:', func_get_args());
	}

	public static function mapDirections($lat, $long) {
		return new Action('dirl:l:', func_get_args());
	}

	public static function addToFav() {
		return new Action('fav');
	}
} 