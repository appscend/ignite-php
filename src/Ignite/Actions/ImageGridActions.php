<?php
namespace Ignite\Actions;

use Ignite\Action;

class ImageGridActions extends ActionBuffer{

	/**
	 * @return Action
	 */
	public static function savePicture() {
		$action = new Action('savePic');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function removePicture() {
		$action = new Action('removePic');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $service valid values: Evernote, Delicious, Facebook, GoogleReader, Instapaper, Pinboard,
	 * ReadItLater, Tumblr, Twitter
	 * @return Action
	 */
	public static function sharePicture($service = null) {
		$actionName = 'sharePic';

		if ($service === null)
			$actionName .= ':';

		$action = new Action($actionName, [$service]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $service valid values: Evernote, Delicious, Facebook, GoogleReader, Instapaper, Pinboard,
	 * ReadItLater, Tumblr, Twitter
	 * @return Action
	 */
	public static function editAndSharePicture($service = null) {
		$actionName = 'editShare';

		if ($service === null)
			$actionName .= ':';

		$action = new Action($actionName, [$service]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function startSlideshow() {
		$action = new Action('slideshow', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function toggleBars() {
		$action = new Action('tn');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function moveNext() {
		$action = new Action('nextp');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function movePrevious() {
		$action = new Action('prevp');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param integer|string $x
	 * @param integer|string $y
	 * @return Action
	 */
	public static function scrollTo($x, $y) {
		$action = new Action('scrollx:y:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function flipSlide() {
		$action = new Action('flipSlide');
		self::$actionBuffer[] = $action;

		return $action;
	}

} 