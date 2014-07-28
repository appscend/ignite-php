<?php

namespace Ignite\Actions;

use Ignite\Action;

class FormActions extends ActionBuffer{

	public static function submit($paramxml, $data , $form , $animation ) {
		$action = new Action('pv:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function submitWithLocation($paramxml, $data , $form , $animation , $accuracy ) {
		$action = new Action('plv:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function modalView($paramxml, $data , $form , $animation , $accuracy , $modalStyle ) {
		$action = new Action('mv:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function modalViewWithLocation($paramxml, $data , $form , $animation , $accuracy , $modalStyle ) {
		$action = new Action('mlv:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function replaceView($paramxml, $data , $form , $animation ) {
		$action = new Action('rv:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function replaceViewWithLocation($paramxml, $data , $form , $animation , $accuracy ) {
		$action = new Action('rlv:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function replaceAll($paramxml, $data , $form , $animation ) {
		$action = new Action('rav:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

	public static function replaceAllWithLocation($paramxml, $data , $form , $animation , $accuracy ) {
		$action = new Action('ralv:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}

} 