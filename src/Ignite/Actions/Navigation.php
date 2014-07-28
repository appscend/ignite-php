<?php

namespace Ignite\Actions;

use Ignite\Action;

class Navigation extends ActionBuffer{

	public static function push($paramxml, $data, $form, $animation) {
		$action = new Action('p:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function pushWithLocation($paramxml, $data, $form, $animation, $accuracy) {
		$action = new Action('pl:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function modalView($paramxml, $data, $form, $animation, $accuracy, $modalStyle) {
		$action = new Action('m:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function modalViewWithLocation($paramxml, $data, $form, $animation, $accuracy, $modalStyle) {
		$action = new Action('ml:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function replace($paramxml, $data, $form, $animation) {
		$action = new Action('r:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function replaceWithLocation($paramxml, $data, $form, $animation, $accuracy) {
		$action = new Action('rl:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function replaceAll($paramxml, $data, $form, $animation) {
		$action = new Action('ra:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function replaceAllWithLocation($paramxml, $data, $form, $animation, $accuracy) {
		$action = new Action('ral:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function closeModal() {
		$action = new Action('closeModal');
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function openBrowserUrl($url) {
		$action = new Action('surl:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function slideShow($paramxml) {
		$action = new Action('s:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function slideShowPostData($paramxml, $data) {
		$action = new Action('s:d:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function refresh() {
		$action = new Action('refresh');
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function previous() {
		$action = new Action('pop');
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function home() {
		$action = new Action('poptr');
		self::$actionBuffer[] = $action;

		return $action;
	}
}