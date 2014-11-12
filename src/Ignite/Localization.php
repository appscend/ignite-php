<?php

namespace Ignite;

use Yosymfony\Toml\Toml;

class Localization {

	private static $strings = [];

	private static $locale = '';

	public static function setLocale($locale) {
		self::$locale = $locale;
		self::$strings = Toml::parse(APP_ROOT_DIR.'/localization/'.$locale.'.toml');
	}

	public static function getLocale() {
		return self::$locale;
	}

	public static function _($string) {
		if (self::$locale == '') {
			EnvironmentManager::app()['ignite_logger']->log('Couldn\'t find translation for string "' . $string . '" for locale ' . self::$locale);

			return null;
		}

		if (isset(self::$strings[$string]))
			return self::$strings[$string];

		EnvironmentManager::app()['ignite_logger']->log('Couldn\'t find translation for string "'.$string.'" for locale '.self::$locale);

		return '';
	}

}