<?php

namespace Ignite\Actions;

use Ignite\Action;

class WidgetActions extends ActionBuffer{

	/**
	 * @param string $id
	 * @return Action
	 */
	public static function refreshElement($id) {
		$action = new Action('refreshl:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $avi
	 * @param string $alpha
	 * @param string $lalpha
	 * @param string $padalpha
	 * @param string $padlalpha
	 * @param string|integer $duration
	 * @return Action
	 */
	public static function changeElementOpacity($avi, $alpha, $lalpha, $padalpha, $padlalpha, $duration = null) {
		$action = new Action('rea:', [$avi, $alpha, $lalpha, $padalpha, $padlalpha, $duration]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $key
	 * @param string $alpha
	 * @param string $lalpha
	 * @param string $padalpha
	 * @param string $padlalpha
	 * @param string|integer $duration
	 * @return Action
	 */
	public static function changeGroupOpacity($key, $alpha, $lalpha, $padalpha, $padlalpha, $duration = null) {
		$action = new Action('reak:', [$avi, $alpha, $lalpha, $padalpha, $padlalpha, $duration]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $avi
	 * @param string $coords
	 * @param array $lcoords
	 * @param array $padcoords
	 * @param array $padlcoords
	 * @param string|integer $duration
	 * @return Action
	 */
	public static function changePositionAndSize($avi, $coords, $lcoords = [], $padcoords = [], $padlcoords = [], $duration = null) {
		$lcoords = implode($lcoords, '::');
		$padcoords = implode($padcoords, '::');
		$padlcoords = implode($padlcoords, '::');
		$action = new Action('ref:', [$avi, $coords, $lcoords, $padcoords, $padlcoords, $duration]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $key
	 * @param string $coords
	 * @param array $lcoords
	 * @param array $padcoords
	 * @param array $padlcoords
	 * @param string|string $duration
	 * @return Action
	 */
	public static function changeGroupPositionAndSize($key, $coords, $lcoords = [], $padcoords = [], $padlcoords = [], $duration = null) {
		$lcoords = implode($lcoords, '::');
		$padcoords = implode($padcoords, '::');
		$padlcoords = implode($padlcoords, '::');
		$action = new Action('refk:', [$key, $coords, $lcoords, $padcoords, $padlcoords, $duration]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $avi
	 * @param string $direction
	 * @param string $v1
	 * @param string $v2
	 * @param string|integer $duration
	 * @param string $lv1
	 * @param string $padv1
	 * @param string $padlv1
	 * @param string $lv2
	 * @param string $padv2
	 * @param string $padlv2
	 * @return Action
	 */
	public static function swapElementLocation($avi, $direction, $v1, $v2, $duration, $lv1 = null, $padv1 = null, $padlv1 = null, $lv2 = null, $padv2 = null, $padlv2 = null) {
		$action = new Action('toggle:', [$avi, $direction, $v1, $v2, $duration, $lv1, $padv1, $padlv1, $lv2, $padv2, $padlv2]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $avi
	 * @param string $value
	 * @param string $property
	 * @return Action
	 */
	public static function changeElementProperties($avi, $value, $property) {
		$action = new Action('rps:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $key
	 * @param string $value
	 * @param string $property
	 * @return Action
	 */
	public static function changeGroupProperties($key, $value, $property) {
		$action = new Action('rpsk:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function snapshot() {
		$action = new Action('snap');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function snapshotToLocalStorage() {
		$action = new Action('snapd');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $url
	 * @return Action
	 */
	public static function snapshotToURL($url) {
		$action = new Action('snapto:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string|integer $x
	 * @param string|integer $y
	 * @return Action
	 */
	public static function scrollTo($x, $y) {
		$action = new Action('scrollx:y:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string|integer $x
	 * @param string|integer $y
	 * @return Action
	 */
	public static function scrollToRelative($x, $y) {
		$action = new Action('scrollrx:y:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string|integer $x
	 * @param string|integer $y
	 * @return Action
	 */
	public static function scrollToPercentage($x, $y) {
		$action = new Action('scrollpx:y:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

} 