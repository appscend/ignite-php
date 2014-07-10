<?php

namespace Ignite\Actions;

use Ignite\Action;

class FormActions {

	public static function submit($paramxml, $data = null, $form = null, $animation = null) {
		return new Action('pv:', func_get_args());
	}

	public static function submitWithLocation($paramxml, $data = null, $form = null, $animation = null, $accuracy = null) {
		return new Action('plv:', func_get_args());
	}

	public static function modalView($paramxml, $data = null, $form = null, $animation = null, $accuracy = null, $modalStyle = null) {
		return new Action('mv:', func_get_args());
	}

	public static function modalViewWithLocation($paramxml, $data = null, $form = null, $animation = null, $accuracy = null, $modalStyle = null) {
		return new Action('mlv:', func_get_args());
	}

	public static function replaceView($paramxml, $data = null, $form = null, $animation = null) {
		return new Action('rv:', func_get_args());
	}

	public static function replaceViewWithLocation($paramxml, $data = null, $form = null, $animation = null, $accuracy = null) {
		return new Action('rlv:', func_get_args());
	}

	public static function replaceAll($paramxml, $data = null, $form = null, $animation = null) {
		return new Action('rav:', func_get_args());
	}

	public static function replaceAllWithLocation($paramxml, $data = null, $form = null, $animation = null, $accuracy = null) {
		return new Action('ralv:', func_get_args());
	}

} 