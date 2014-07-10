<?php
namespace Ignite\Actions;

use Ignite\Action;

class ImageGridActions {

	public static function savePicture() {
		return new Action('savePic');
	}

	public static function removePicture() {
		return new Action('removePic');
	}

	public static function sharePicture($service = null) {
		$actionName = 'sharePic';

		if (isset($service))
			$actionName .= ':';

		return new Action($actionName, func_get_args());
	}

	public static function editAndSharePicture($service = null) {
		$actionName = 'editShare';

		if (isset($service))
			$actionName .= ':';

		return new Action($actionName, func_get_args());
	}

	public static function startSlideshow() {
		return new Action('slideshow');
	}

	public static function toggleBars() {
		return new Action('tn');
	}

	public static function moveNext() {
		return new Action('nextp');
	}

	public static function movePrevious() {
		return new Action('prevp');
	}

	public static function scrollTo($x, $y) {
		return new Action('scrollx:y:', func_get_args());
	}

	public static function flipSlide() {
		return new Action('flipSlide');
	}

} 