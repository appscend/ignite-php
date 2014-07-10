<?php

namespace Ignite\Actions;

use Ignite\Action;

class WidgetActions {

	public static function refreshElement($id) {
		return new Action('refreshl:', func_get_args());
	}

	public static function changeElementOpacity($avi, $alpha, $lalpha, $padalpha, $padlalpha, $duration = null) {
		return new Action('rea:', func_get_args());
	}

	public static function changeGroupOpacity($key, $alpha, $lalpha, $padalpha, $padlalpha, $duration = null) {
		return new Action('reak:', func_get_args());
	}

	public static function changePositionAndSize($avi, $coords, $lcoords = null, $padcoords = null, $padlcoords = null, $duration = null) {
		return new Action('ref:', func_get_args());
	}

	public static function changeGroupPositionAndSize($key, $coords, $lcoords = null, $padcoords = null, $padlcoords = null, $duration = null) {
		return new Action('refk:', func_get_args());
	}

	public static function swapElementLocation($avi, $direction, $v1, $lv1 = '', $padv1 = '', $padlv1 = '', $v2, $lv2 = '', $padv2, $padlv2 = '', $duration) {
		return new Action('toggle:', func_get_args());
	}

	public static function changeElementProperties($avi, $value, $property) {
		return new Action('rps:', func_get_args());
	}

	public static function changeGroupProperties($key, $value, $property) {
		return new Action('rpsk:', func_get_args());
	}
	public static function snapshot() {
		return new Action('snap');
	}

	public static function snapshotToLocalStorage() {
		return new Action('snapd');
	}

	public static function snapshotToURL($url) {
		return new Action('snapto:', func_get_args());
	}

	public static function scrollTo($x, $y) {
		return new Action('scrollx:y:', func_get_args());
	}

	public static function scrollToRelative($x, $y) {
		return new Action('scrollrx:y:', func_get_args());
	}

	public static function scrollToPercentage($x, $y) {
		return new Action('scrollpx:y:', func_get_args());
	}

} 