<?php

namespace Ignite\Actions;

use Ignite\Action;

class Navigation extends ActionBuffer{

	public static function push($paramxml, $data = null, $form = null, $animation = null) {
		$action = new Action('p:', [$paramxml, $data, $form, $animation]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function pushWithLocation($paramxml, $data = null, $form = null, $animation = null, $accuracy = null) {
		$action = new Action('pl:', [$paramxml, $data, $form, $animation, $accuracy]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function modalView($paramxml, $data = null, $form = null, $animation = null, $accuracy = null, $modalStyle = null) {
		$action = new Action('m:', [$paramxml, $data, $form, $animation, $accuracy, $modalStyle]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function modalViewWithLocation($paramxml, $data = null, $form = null, $animation = null, $accuracy = null, $modalStyle = null) {
		$action = new Action('ml:', [$paramxml, $data, $form, $animation, $accuracy, $modalStyle]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function replace($paramxml, $data = null, $form = null, $animation = null) {
		$action = new Action('r:', [$paramxml, $data, $form, $animation]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function replaceWithLocation($paramxml, $data = null, $form = null, $animation = null, $accuracy = null) {
		$action = new Action('rl:', [$paramxml, $data, $form, $animation, $accuracy]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function replaceAll($paramxml, $data = null, $form = null, $animation = null) {
		$action = new Action('ra:', [$paramxml, $data, $form, $animation]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function replaceAllWithLocation($paramxml, $data = null, $form = null, $animation = null, $accuracy = null) {
		$action = new Action('ral:', [$paramxml, $data, $form, $animation, $accuracy]);
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