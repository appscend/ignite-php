<?php
namespace Ignite\Actions;

use Ignite\Action;

class ImageGridActions extends ActionBuffer{

	public static function savePicture() {
		$action = new Action('savePic');
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function removePicture() {
		$action = new Action('removePic');
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function sharePicture($service) {
		$actionName = 'sharePic';

		if ($service === '')
			$actionName .= ':';

		$action = new Action($actionName, func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function editAndSharePicture($service) {
		$actionName = 'editShare';

		if ($service === '')
			$actionName .= ':';

		$action = new Action($actionName, func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function startSlideshow() {
		$action = new Action('slideshow', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function toggleBars() {
		$action = new Action('tn');
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function moveNext() {
		$action = new Action('nextp');
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function movePrevious() {
		$action = new Action('prevp');
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function scrollTo($x, $y) {
		$action = new Action('scrollx:y:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function flipSlide() {
		$action = new Action('flipSlide');
		self::$actionBuffer[] = $action;

		return $action;
	}

} 