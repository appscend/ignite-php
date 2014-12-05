<?php

namespace Ignite\Actions;

use Ignite\Action;
use Ignite\ViewStub;

class Navigation extends ActionBuffer{

	/**
	 * @param string|ViewStub $paramxml
	 * @param string $data
	 * @param string $form
	 * @param string $animation
	 * @return Action
	 */
	public static function push($paramxml, $data = null, $form = null, $animation = null) {
		if ($paramxml instanceof ViewStub)
			$paramxml = $paramxml->getPath();

		$action = new Action('p:', [$paramxml, $data, $form, $animation]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function quietPush($paramxml, $data = null, $form = null, $animation = null) {
		if ($paramxml instanceof ViewStub)
			$paramxml = $paramxml->getPath();

		$action = new Action('qp:', [$paramxml, $data, $form, $animation]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string|ViewStub $paramxml
	 * @param string $data
	 * @param string $form
	 * @param string $animation
	 * @param string $accuracy
	 * @return Action
	 */
	public static function pushWithLocation($paramxml, $data = null, $form = null, $animation = null, $accuracy = null) {
		if ($paramxml instanceof ViewStub)
			$paramxml = $paramxml->getPath();

		$action = new Action('pl:', [$paramxml, $data, $form, $animation, $accuracy]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string|ViewStub $paramxml
	 * @param string $data
	 * @param string $form
	 * @param string $animation
	 * @param string $accuracy
	 * @return Action
	 */
	public static function quietPushWithLocation($paramxml, $data = null, $form = null, $animation = null, $accuracy = null) {
		if ($paramxml instanceof ViewStub)
			$paramxml = $paramxml->getPath();

		$action = new Action('qpl:', [$paramxml, $data, $form, $animation, $accuracy]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string|ViewStub $paramxml
	 * @param string $data
	 * @param string $form
	 * @param string $animation
	 * @param string $accuracy
	 * @param string $modalStyle
	 * @return Action
	 */
	public static function modalView($paramxml, $data = null, $form = null, $animation = null, $accuracy = null, $modalStyle = null) {
		if ($paramxml instanceof ViewStub)
			$paramxml = $paramxml->getPath();

		$action = new Action('m:', [$paramxml, $data, $form, $animation, $accuracy, $modalStyle]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string|ViewStub $paramxml
	 * @param string $data
	 * @param string $form
	 * @param string $animation
	 * @param string $accuracy
	 * @param string $modalStyle
	 * @return Action
	 */
	public static function quietModalView($paramxml, $data = null, $form = null, $animation = null, $accuracy = null, $modalStyle = null) {
		if ($paramxml instanceof ViewStub)
			$paramxml = $paramxml->getPath();

		$action = new Action('qm:', [$paramxml, $data, $form, $animation, $accuracy, $modalStyle]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string|ViewStub $paramxml
	 * @param string $data
	 * @param string $form
	 * @param string $animation
	 * @param string $accuracy
	 * @param string $modalStyle
	 * @return Action
	 */
	public static function modalViewWithLocation($paramxml, $data = null, $form = null, $animation = null, $accuracy = null, $modalStyle = null) {
		if ($paramxml instanceof ViewStub)
			$paramxml = $paramxml->getPath();

		$action = new Action('ml:', [$paramxml, $data, $form, $animation, $accuracy, $modalStyle]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string|ViewStub $paramxml
	 * @param string $data
	 * @param string $form
	 * @param string $animation
	 * @param string $accuracy
	 * @param string $modalStyle
	 * @return Action
	 */
	public static function quietModalViewWithLocation($paramxml, $data = null, $form = null, $animation = null, $accuracy = null, $modalStyle = null) {
		if ($paramxml instanceof ViewStub)
			$paramxml = $paramxml->getPath();

		$action = new Action('qml:', [$paramxml, $data, $form, $animation, $accuracy, $modalStyle]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string|ViewStub $paramxml
	 * @param string $data
	 * @param string $form
	 * @param string $animation
	 * @return Action
	 */
	public static function replace($paramxml, $data = null, $form = null, $animation = null) {
		if ($paramxml instanceof ViewStub)
			$paramxml = $paramxml->getPath();

		$action = new Action('r:', [$paramxml, $data, $form, $animation]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string|ViewStub $paramxml
	 * @param string $data
	 * @param string $form
	 * @param string $animation
	 * @return Action
	 */
	public static function quietReplace($paramxml, $data = null, $form = null, $animation = null) {
		if ($paramxml instanceof ViewStub)
			$paramxml = $paramxml->getPath();

		$action = new Action('qr:', [$paramxml, $data, $form, $animation]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string|ViewStub $paramxml
	 * @param string $data
	 * @param string $form
	 * @param string $animation
	 * @param string $accuracy
	 * @return Action
	 */
	public static function replaceWithLocation($paramxml, $data = null, $form = null, $animation = null, $accuracy = null) {
		if ($paramxml instanceof ViewStub)
			$paramxml = $paramxml->getPath();

		$action = new Action('rl:', [$paramxml, $data, $form, $animation, $accuracy]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string|ViewStub $paramxml
	 * @param string $data
	 * @param string $form
	 * @param string $animation
	 * @param string $accuracy
	 * @return Action
	 */
	public static function quietReplaceWithLocation($paramxml, $data = null, $form = null, $animation = null, $accuracy = null) {
		if ($paramxml instanceof ViewStub)
			$paramxml = $paramxml->getPath();

		$action = new Action('qrl:', [$paramxml, $data, $form, $animation, $accuracy]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string|ViewStub $paramxml
	 * @param string $data
	 * @param string $form
	 * @param string $animation
	 * @return Action
	 */
	public static function replaceAll($paramxml, $data = null, $form = null, $animation = null) {
		if ($paramxml instanceof ViewStub)
			$paramxml = $paramxml->getPath();

		$action = new Action('ra:', [$paramxml, $data, $form, $animation]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string|ViewStub $paramxml
	 * @param string $data
	 * @param string $form
	 * @param string $animation
	 * @return Action
	 */
	public static function quietReplaceAll($paramxml, $data = null, $form = null, $animation = null) {
		if ($paramxml instanceof ViewStub)
			$paramxml = $paramxml->getPath();

		$action = new Action('qra:', [$paramxml, $data, $form, $animation]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string|ViewStub $paramxml
	 * @param string $data
	 * @param string $form
	 * @param string $animation
	 * @param string $accuracy
	 * @return Action
	 */
	public static function replaceAllWithLocation($paramxml, $data = null, $form = null, $animation = null, $accuracy = null) {
		if ($paramxml instanceof ViewStub)
			$paramxml = $paramxml->getPath();

		$action = new Action('ral:', [$paramxml, $data, $form, $animation, $accuracy]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string|ViewStub $paramxml
	 * @param string $data
	 * @param string $form
	 * @param string $animation
	 * @param string $accuracy
	 * @return Action
	 */
	public static function quietReplaceAllWithLocation($paramxml, $data = null, $form = null, $animation = null, $accuracy = null) {
		if ($paramxml instanceof ViewStub)
			$paramxml = $paramxml->getPath();

		$action = new Action('qral:', [$paramxml, $data, $form, $animation, $accuracy]);
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function closeModal() {
		$action = new Action('closeModal');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string $url
	 * @return Action
	 */
	public static function openBrowserUrl($url) {
		$action = new Action('surl:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string|ViewStub $paramxml
	 * @return Action
	 */
	public static function slideShow($paramxml) {
		if ($paramxml instanceof ViewStub)
			$paramxml = $paramxml->getPath();

		$action = new Action('s:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @param string|ViewStub $paramxml
	 * @param string $data
	 * @return Action
	 */
	public static function slideShowPostData($paramxml, $data) {
		if ($paramxml instanceof ViewStub)
			$paramxml = $paramxml->getPath();

		$action = new Action('s:d:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function refresh() {
		$action = new Action('refresh');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function pop() {
		$action = new Action('pop');
		self::$actionBuffer[] = $action;

		return $action;
	}

	/**
	 * @return Action
	 */
	public static function home() {
		$action = new Action('poptr');
		self::$actionBuffer[] = $action;

		return $action;
	}
}