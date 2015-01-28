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

	public static function filter($text) {
		$action = new Action('filter:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function unfilter() {
		$action = new Action('filter:');
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
	public static function changeElementOpacity($avi, $alpha, $lalpha, $padalpha, $padlalpha, $ff4alpha, $ff4lalpha, $duration = null) {
		$action = new Action('rea:', [$avi, $alpha, $lalpha, $padalpha, $padlalpha, $ff4alpha, $ff4lalpha, $duration]);
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
	public static function changeElementOpacityByKey($key, $alpha, $lalpha, $padalpha, $padlalpha, $ff4alpha, $ff4lalpha, $duration = null) {
		$action = new Action('reak:', [$key, $alpha, $lalpha, $padalpha, $padlalpha, $ff4alpha, $ff4lalpha, $duration]);
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
	public static function changePositionAndSize($duration = null, $avi, $coords = [], $lcoords = [], $padcoords = [], $padlcoords = []) {
		$coords = implode($coords, '::');
		$lcoords = implode($lcoords, '::');
		$padcoords = implode($padcoords, '::');
		$padlcoords = implode($padlcoords, '::');

		$actionParams = [$avi];
		if ($coords != '')
			$actionParams[] = $coords;
		if ($lcoords != '') {
			if ($coords == '')
				$actionParams[] = '::::::';
			$actionParams[] = $lcoords;
		}
		if ($padcoords != '') {
			if ($coords == '')
				$actionParams[] = '::::::';
			if ($lcoords == '')
				$actionParams[] = '::::::';
			$actionParams[] = $padcoords;
		}
		if ($padlcoords != '') {
			if ($coords == '')
				$actionParams[] = '::::::';
			if ($lcoords == '')
				$actionParams[] = '::::::';
			if ($padcoords == '')
				$actionParams[] = '::::::';
			$actionParams[] = $padlcoords;
		}

		$actionParams[] = $duration;

		$action = new Action('ref:', $actionParams);
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
	public static function changePositionAndSizeByKey($duration = null, $key, $coords = [], $lcoords = [], $padcoords = [], $padlcoords = []) {
		$coords = implode($coords, '::');
		$lcoords = implode($lcoords, '::');
		$padcoords = implode($padcoords, '::');
		$padlcoords = implode($padlcoords, '::');

		$actionParams = [$key];
		if ($coords != '')
			$actionParams[] = $coords;
		if ($lcoords != '') {
			if ($coords == '')
				$actionParams[] = '::::::';
			$actionParams[] = $lcoords;
		}
		if ($padcoords != '') {
			if ($coords == '')
				$actionParams[] = '::::::';
			if ($lcoords == '')
				$actionParams[] = '::::::';
			$actionParams[] = $padcoords;
		}
		if ($padlcoords != '') {
			if ($coords == '')
				$actionParams[] = '::::::';
			if ($lcoords == '')
				$actionParams[] = '::::::';
			if ($padcoords == '')
				$actionParams[] = '::::::';
			$actionParams[] = $padlcoords;
		}

		$actionParams[] = $duration;

		$action = new Action('refk:', $actionParams);
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
	public static function swapElementLocation($avi, $direction, $v1, $v2, $duration, $lv1 = null, $lv2 = null, $padv1 = null, $padv2 = null, $padlv1 = null, $padlv2 = null) {

		$actionParams = [$avi, $direction, $v1];
		if ($lv1 != null)
			$actionParams[] = $lv1;

		if ($padv1 != '') {
			if ($lv1 == null)
				$actionParams[] = '::';
			$actionParams[] = $padv1;
		}

		if ($padlv1 != '') {
			if ($lv1 == null)
				$actionParams[] = '::';
			if ($padv1 == null)
				$actionParams[] = '::';
			$actionParams[] = $padlv1;
		}

		$actionParams[] = $v2;

		if ($lv2 != null)
			$actionParams[] = $lv2;

		if ($padv2 != '') {
			if ($lv2 == null)
				$actionParams[] = '::';
			$actionParams[] = $padv2;
		}

		if ($padlv2 != '') {
			if ($lv2 == null)
				$actionParams[] = '::';
			if ($padv2 == null)
				$actionParams[] = '::';
			$actionParams[] = $padlv2;
		}

		$actionParams[] = $duration;

		$action = new Action('toggle:', $actionParams);
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
	public static function changePropertiesByKey($key, $value, $property) {
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