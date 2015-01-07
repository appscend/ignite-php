<?php

namespace Ignite\Actions;

use Ignite\Action;

class System extends ActionBuffer{

	/**
	 * @param string $name
	 * @param string $params
	 * @return Action
	 */
	public static function callJSFunction($name, $params) {
		$action = new Action('func:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $message
	 * @return Action
	 */
	public static function alert($message) {
		$action = new Action('alert:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $message
	 * @return Action
	 */
	public static function removeAds($message) {
		$action = new Action('dropads:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $url
	 * @return Action
	 */
	public static function saveImage($url) {
		$action = new Action('si:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $url
	 * @return Action
	 */
	public static function openURL($url) {
		$action = new Action('url:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $lat
	 * @param string $long
	 * @return Action
	 */
	public static function mapDirections($lat, $long) {
		$action = new Action('dirl:l:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function addToFav() {
		$action = new Action('fav');
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function selectFromGallery($gid, $formKey) {
		$action = new Action('seligid:k:s:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function takeNewPicture($gid, $formKey) {
		$action = new Action('snapigid:k:s:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}
} 