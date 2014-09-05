<?php

namespace Ignite;

define("LIB_ROOT_DIR", dirname(dirname(__DIR__)));
define("APP_ROOT_DIR", dirname('../../..'));
define("MODULES_DIR", APP_ROOT_DIR.'/modules');
define("ASSETS_DIR", APP_ROOT_DIR.'/assets');
define("CONFIG_DIR", APP_ROOT_DIR.'/config');

use Ignite\Providers\Logger;
use Ignite\Providers\MemcachedProvider;
use Silex\Application as SilexApp;
use Silex\Provider as SilexProvider;
use Symfony\Component\HttpFoundation\Request;
use Yosymfony\Silex\ConfigServiceProvider\ConfigServiceProvider;

class Application extends SilexApp {
	use SilexApp\UrlGeneratorTrait;
	use SilexApp\MonologTrait;
	use Application\ConfigTrait;
	use Application\GELFTrait;
	
	const IGNITE_VERSION = '0.0.1';

	private static $blacklistPostKeys = [
		'udata',
		'carrier',
		'udid',
		'device',
		'os',
		'timestamp',
		'timezone',
		'platform'
	];

	private $currentRoute = '';
	private $currentModule = '';

	function __construct(array $values = array()) {
		parent::__construct($values);
		
		$this->register(new SilexProvider\UrlGeneratorServiceProvider());
		
		$configurationPaths = [CONFIG_DIR];
		$modulePaths = glob(MODULES_DIR . "/*" , GLOB_ONLYDIR);
		foreach ($modulePaths as $modulePath)
			array_push($configurationPaths, $modulePath, $modulePath."/config");
		
		$this->register(new ConfigServiceProvider(
		    $configurationPaths
		));
		
		$this->register(new Providers\LocatorServiceProvider());
		$this['locator.directories'] = [LIB_ROOT_DIR, APP_ROOT_DIR];
		
		$this->register(new \Whoops\Provider\Silex\WhoopsServiceProvider);
		
		$this['dispatcher']->addSubscriber(new EventListener\ViewToResponseListener($this));

		$this->register(new EnvironmentManager());
		$this->register(new Logger($this));

		if ($this['env']['memcache.enabled'] == 'true')
			$this->register(new MemcachedProvider());

		$this->before(function(Request $req){
			$this->setRouteName($req->get('_route'));
		});



	}

	public function getModuleName() {
		return $this->currentModule;
	}

	public function setModuleName($v) {
		$this->currentModule = $v;
	}

	public function getRouteName() {
		return $this->currentRoute;
	}

	private function setRouteName($v) {
		$this->currentRoute = $v;
	}

	public static function getBlacklistPostKeys() {
		return self::$blacklistPostKeys;
	}

	public function getAssetsPath() {
		return $this['env']['app.assets_path'];
	}

	public function getStaticXMLPath() {
		return $this['env']['app.static_xml_path'];
	}

	public function getWebPath() {
		return$this['env']['app.web_path'];
	}
}
