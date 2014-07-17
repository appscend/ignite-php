<?php
namespace Ignite\Views;

use Ignite\Registry;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class ViewConfigContainer extends Registry implements ConfigurationInterface{

	const CONFIG_PATH 					= '/src/Ignite/Config';
	const GENERIC_CONFIG_FILE_SPEC 		= 'generic.json';

	private $configSpec = [];

	public function __construct() {
		parent::__construct('cfg');
		$this->configSpec = json_decode(file_get_contents(ROOT_DIR.self::CONFIG_PATH.'/'.self::GENERIC_CONFIG_FILE_SPEC), true);
	}

	public function appendConfigFile($filepath) {
		if (!is_readable(ROOT_DIR.self::CONFIG_PATH.'/'.$filepath))
			throw new FileNotFoundException("Configuration file '".ROOT_DIR.self::CONFIG_PATH.'/'.$filepath."' not readable or not found");

		$this->configSpec['cfg'] = array_merge($this->configSpec['cfg'], json_decode(file_get_contents(ROOT_DIR.self::CONFIG_PATH.'/'.$filepath)['cfg'], true));
	}

	public function getConfigSpec() {
		return $this->configSpec;
	}

	public function getConfigTreeBuilder() {

	}

} 