<?php

namespace Ignite\Actions;

use Ignite\Action;

class FormActions {

	public static function submit($paramxml, $data , $form , $animation ) {
		return new Action('pv:', func_get_args());
	}

	public static function submitWithLocation($paramxml, $data , $form , $animation , $accuracy ) {
		return new Action('plv:', func_get_args());
	}

	public static function modalView($paramxml, $data , $form , $animation , $accuracy , $modalStyle ) {
		return new Action('mv:', func_get_args());
	}

	public static function modalViewWithLocation($paramxml, $data , $form , $animation , $accuracy , $modalStyle ) {
		return new Action('mlv:', func_get_args());
	}

	public static function replaceView($paramxml, $data , $form , $animation ) {
		return new Action('rv:', func_get_args());
	}

	public static function replaceViewWithLocation($paramxml, $data , $form , $animation , $accuracy ) {
		return new Action('rlv:', func_get_args());
	}

	public static function replaceAll($paramxml, $data , $form , $animation ) {
		return new Action('rav:', func_get_args());
	}

	public static function replaceAllWithLocation($paramxml, $data , $form , $animation , $accuracy ) {
		return new Action('ralv:', func_get_args());
	}

} 