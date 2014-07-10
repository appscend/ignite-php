<?php

namespace Ignite\Actions;

use Ignite\Action;

class CoverFlowActions {

	public static function startSlideshow() {
		return new Action('slideshow');
	}

	public static function flip() {
		return new Action('flipSlide');
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