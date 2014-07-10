<?php

namespace Ignite\Actions;

use Ignite\Action;

class Navigation {

	public static function push($paramxml, $data = null, $form = null, $animation = null) {
		return new Action('p:', func_get_args());
	}

	public static function pushWithLocation($paramxml, $data = null, $form = null, $animation = null, $accuracy = null) {
		return new Action('pl:', func_get_args());
	}

	public static function modalView($paramxml, $data = null, $form = null, $animation = null, $accuracy = null, $modalStyle = null) {
		return new Action('m:', func_get_args());
	}

	public static function modalViewWithLocation($paramxml, $data = null, $form = null, $animation = null, $accuracy = null, $modalStyle = null) {
		return new Action('ml:', func_get_args());
	}

	public static function replace($paramxml, $data = null, $form = null, $animation = null) {
		return new Action('r:', func_get_args());
	}

	public static function replaceWithLocation($paramxml, $data = null, $form = null, $animation = null, $accuracy = null) {
		return new Action('rl:', func_get_args());
	}

	public static function replaceAll($paramxml, $data = null, $form = null, $animation = null) {
		return new Action('ra:', func_get_args());
	}

	public static function replaceAllWithLocation($paramxml, $data = null, $form = null, $animation = null, $accuracy = null) {
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