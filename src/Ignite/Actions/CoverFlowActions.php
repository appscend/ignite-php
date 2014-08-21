<?php

namespace Ignite\Actions;

use Ignite\Action;

class CoverFlowActions extends ActionBuffer {

	/**
	 * @return Action
	 */
	public static function startSlideshow() {
		$action = new Action('slideshow');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function flip() {
		$action = new Action('flipSlide');
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
	 * @param integer|string $x
	 * @param integer|string $y
	 * @return Action
	 */
	public static function scrollToRelative($x, $y) {
		$action = new Action('scrollrx:y:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param integer|string $x
	 * @param integer|string $y
	 * @return Action
	 */
	public static function scrollToPercentage($x, $y) {
		$action = new Action('scrollpx:y:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

} 