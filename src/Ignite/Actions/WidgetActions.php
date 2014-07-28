<?php

namespace Ignite\Actions;

use Ignite\Action;

class WidgetActions extends ActionBuffer{

	public static function refreshElement($id) {
		$action = new Action('refreshl:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function changeElementOpacity($avi, $alpha, $lalpha, $padalpha, $padlalpha, $duration) {
		$action = new Action('rea:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function changeGroupOpacity($key, $alpha, $lalpha, $padalpha, $padlalpha, $duration) {
		$action = new Action('reak:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function changePositionAndSize($avi, $coords, $lcoords, $padcoords, $padlcoords, $duration) {
		$action = new Action('ref:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function changeGroupPositionAndSize($key, $coords, $lcoords, $padcoords, $padlcoords, $duration) {
		$action = new Action('refk:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function swapElementLocation($avi, $direction, $v1, $lv1, $padv1, $padlv1, $v2, $lv2, $padv2, $padlv2, $duration) {
		$action = new Action('toggle:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function changeElementProperties($avi, $value, $property) {
		$action = new Action('rps:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function changeGroupProperties($key, $value, $property) {
		$action = new Action('rpsk:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}
	public static function snapshot() {
		$action = new Action('snap');
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function snapshotToLocalStorage() {
		$action = new Action('snapd');
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function snapshotToURL($url) {
		$action = new Action('snapto:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function scrollTo($x, $y) {
		$action = new Action('scrollx:y:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function scrollToRelative($x, $y) {
		$action = new Action('scrollrx:y:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function scrollToPercentage($x, $y) {
		$action = new Action('scrollpx:y:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

} 