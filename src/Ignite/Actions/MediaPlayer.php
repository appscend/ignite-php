<?php

namespace Ignite\Actions;

use Ignite\Action;

class MediaPlayer extends ActionBuffer {

	public static function appendAll() {
		$action = new Action('mpaa');
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function replaceAll() {
		$action = new Action('mpraa');
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function replaceAllAndPlay() {
		$action = new Action('mprp');
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function appendAllNoPopup() {
		$action = new Action('mpaaq');
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function replaceAllNoPopup() {
		$action = new Action('mprpq');
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function append($index) {
		$actionName = 'ap';

		if ($index === '')
			$actionName .= ':';

		$action = new Action($actionName, func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function appendAndPlay($index) {
		$actionName = 'anp';

		if ($index === '')
			$actionName .= ':';

		$action = new Action($actionName, func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function appendNoPopup($index) {
		$actionName = 'apq';

		if ($index === '')
			$actionName .= ':';

		$action = new Action($actionName, func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function appendNoPopupAndPlay($index) {
		$actionName = 'anpq';

		if ($index === '')
			$actionName .= ':';

		$action = new Action($actionName, func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function enqueueSong($name, $description, $mediaLink, $image, $shopLink) {
		$action = new Action('am:d:m:i:l:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function enqueueSongAndPlay($name, $description, $mediaLink, $image, $shopLink) {
		$action = new Action('amnp:d:m:i:l:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function enqueueSongNoPopup($name, $description, $mediaLink, $image, $shopLink) {
		$action = new Action('amq:d:m:i:l:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function enqueueSongNoPopupAndPlay($name, $description, $mediaLink, $image, $shopLink) {
		$action = new Action('amnpq:d:m:i:l:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function toggleOverlayVisibility() {
		$action = new Action('tmp');
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function previous() {
		$action = new Action('prev');
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function next() {
		$action = new Action('next');
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function clearPlaylist() {
		$action = new Action('mpclr');
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function togglePlay() {
		$action = new Action('play');
		self::$actionBuffer[] = $action;

		return $action;
	}

} 