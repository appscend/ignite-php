<?php

namespace Ignite\Actions;

use Ignite\Action;

class MediaPlayer {

	public static function appendAll() {
		return new Action('mpaa');
	}

	public static function replaceAll() {
		return new Action('mpraa');
	}

	public static function replaceAllAndPlay() {
		return new Action('mprp');
	}

	public static function appendAllNoPopup() {
		return new Action('mpaaq');
	}

	public static function replaceAllNoPopup() {
		return new Action('mprpq');
	}

	public static function append($index) {
		$actionName = 'ap';

		if ($index === '')
			$actionName .= ':';

		return new Action($actionName, func_get_args());
	}

	public static function appendAndPlay($index) {
		$actionName = 'anp';

		if ($index === '')
			$actionName .= ':';

		return new Action($actionName, func_get_args());
	}

	public static function appendNoPopup($index) {
		$actionName = 'apq';

		if ($index === '')
			$actionName .= ':';

		return new Action($actionName, func_get_args());
	}

	public static function appendNoPopupAndPlay($index) {
		$actionName = 'anpq';

		if ($index === '')
			$actionName .= ':';

		return new Action($actionName, func_get_args());
	}

	public static function enqueueSong($name, $description, $mediaLink, $image, $shopLink) {
		return new Action('am:d:m:i:l:', func_get_args());
	}

	public static function enqueueSongAndPlay($name, $description, $mediaLink, $image, $shopLink) {
		return new Action('amnp:d:m:i:l:', func_get_args());
	}

	public static function enqueueSongNoPopup($name, $description, $mediaLink, $image, $shopLink) {
		return new Action('amq:d:m:i:l:', func_get_args());
	}

	public static function enqueueSongNoPopupAndPlay($name, $description, $mediaLink, $image, $shopLink) {
		return new Action('amnpq:d:m:i:l:', func_get_args());
	}

	public static function toggleOverlayVisibility() {
		return new Action('tmp');
	}

	public static function previous() {
		return new Action('prev');
	}

	public static function next() {
		return new Action('next');
	}

	public static function clearPlaylist() {
		return new Action('mpclr');
	}

	public static function togglePlay() {
		return new Action('play');
	}

} 