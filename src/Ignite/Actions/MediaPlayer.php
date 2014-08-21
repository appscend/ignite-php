<?php

namespace Ignite\Actions;

use Ignite\Action;

class MediaPlayer extends ActionBuffer {

	/**
	 * @return Action
	 */
	public static function appendAll() {
		$action = new Action('mpaa');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function replaceAll() {
		$action = new Action('mpraa');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function replaceAllAndPlay() {
		$action = new Action('mprp');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function appendAllNoPopup() {
		$action = new Action('mpaaq');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function replaceAllNoPopup() {
		$action = new Action('mprpq');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string|integer $index
	 * @return Action
	 */
	public static function append($index = null) {
		$actionName = 'ap';

		if ($index === null)
			$actionName .= ':';

		$action = new Action($actionName, [$index]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string|integer $index
	 * @return Action
	 */
	public static function appendAndPlay($index = null) {
		$actionName = 'anp';

		if ($index === null)
			$actionName .= ':';

		$action = new Action($actionName, [$index]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string|integer $index
	 * @return Action
	 */
	public static function appendNoPopup($index = null) {
		$actionName = 'apq';

		if ($index === null)
			$actionName .= ':';

		$action = new Action($actionName, [$index]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string|integer $index
	 * @return Action
	 */
	public static function appendNoPopupAndPlay($index = null) {
		$actionName = 'anpq';

		if ($index === null)
			$actionName .= ':';

		$action = new Action($actionName, [$index]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $name
	 * @param string $description
	 * @param string $mediaLink
	 * @param string $image
	 * @param string $shopLink
	 * @return Action
	 */
	public static function enqueueSong($name, $description, $mediaLink, $image, $shopLink) {
		$action = new Action('am:d:m:i:l:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $name
	 * @param string $description
	 * @param string $mediaLink
	 * @param string $image
	 * @param string $shopLink
	 * @return Action
	 */
	public static function enqueueSongAndPlay($name, $description, $mediaLink, $image, $shopLink) {
		$action = new Action('amnp:d:m:i:l:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $name
	 * @param string $description
	 * @param string $mediaLink
	 * @param string $image
	 * @param string $shopLink
	 * @return Action
	 */
	public static function enqueueSongNoPopup($name, $description, $mediaLink, $image, $shopLink) {
		$action = new Action('amq:d:m:i:l:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $name
	 * @param string $description
	 * @param string $mediaLink
	 * @param string $image
	 * @param string $shopLink
	 * @return Action
	 */
	public static function enqueueSongNoPopupAndPlay($name, $description, $mediaLink, $image, $shopLink) {
		$action = new Action('amnpq:d:m:i:l:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function toggleOverlayVisibility() {
		$action = new Action('tmp');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function previous() {
		$action = new Action('prev');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function next() {
		$action = new Action('next');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function clearPlaylist() {
		$action = new Action('mpclr');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function togglePlay() {
		$action = new Action('play');
		self::$actionBuffer[] = $action;

		return $action;
	}

} 