<?php

namespace Ignite\Actions;

use Ignite\Action;

class Navigation {

	public static function push($paramxml, $data, $form, $animation) {
		return new Action('p:', func_get_args());
	}

	public static function pushWithLocation($paramxml, $data, $form, $animation, $accuracy) {
		return new Action('pl:', func_get_args());
	}

	public static function modalView($paramxml, $data, $form, $animation, $accuracy, $modalStyle) {
		return new Action('m:', func_get_args());
	}

	public static function modalViewWithLocation($paramxml, $data, $form, $animation, $accuracy, $modalStyle) {
		return new Action('ml:', func_get_args());
	}

	public static function replace($paramxml, $data, $form, $animation) {
		return new Action('r:', func_get_args());
	}

	public static function replaceWithLocation($paramxml, $data, $form, $animation, $accuracy) {
		return new Action('rl:', func_get_args());
	}

	public static function replaceAll($paramxml, $data, $form, $animation) {
		return new Action('ra:', func_get_args());
	}

	public static function replaceAllWithLocation($paramxml, $data, $form, $animation, $accuracy) {
		return new Action('ral:', func_get_args());
	}

	public static function closeModal() {
		return new Action('closeModal');
	}

	public static function openBrowserUrl($url) {
		return new Action('surl:', func_get_args());
	}

	public static function slideShow($paramxml) {
		return new Action('s:', func_get_args());
	}

	public static function slideShowPostData($paramxml, $data) {
		return new Action('s:d:', func_get_args());
	}

	public static function refresh() {
		return new Action('refresh');
	}

	public static function previous() {
		return new Action('pop');
	}

	public static function home() {
		return new Action('poptr');
	}
}