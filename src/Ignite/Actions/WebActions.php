<?php

namespace Ignite\Actions;

use Ignite\Action;

class WebActions extends ActionBuffer {

	/**
	 * @return Action
	 */
	public static function refresh() {
		$action = new Action('r');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function back() {
		$action = new Action('b');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function forward() {
		$action = new Action('f');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function bookmark() {
		$action = new Action('fav');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function increaseFontSize() {
		$action = new Action('fp');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function decreaseFontSize() {
		$action = new Action('fm');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function cycleFontSizes() {
		$action = new Action('fa');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $service
	 * @return Action
	 */
	public static function sharePage($service = null) {
		$actionName = 'share';

		if ($service !== null)
			$actionName .= ':';

		$action = new Action($actionName, [$service]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function loadNextItem() {
		$action = new Action('ni');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function loadPreviousItem() {
		$action = new Action('pi');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function getInfoEvent() {
		$action = new Action('getinfo');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $fname
	 * @return Action
	 */
	public static function callJSFunction($fname) {
		$action = new Action('trigger:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $id
	 * @return Action
	 */
	public static function getFormEvent($id) {
		$action = new Action('getform:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $url
	 * @return Action
	 */
	public static function getDataEvent($url) {
		$action = new Action('getdata:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}
} 