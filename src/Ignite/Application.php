<?php

namespace Ignite;

define("APP_ROOT_DIR", realpath('.'));
define("LIB_ROOT_DIR", APP_ROOT_DIR);
define("CONFIG_DIR", APP_ROOT_DIR.'/config');

use Ignite\Providers\Logger;
use Ignite\Providers\MemcachedProvider;
use Silex\Application as SilexApp;
use Silex\Provider as SilexProvider;
use Symfony\Component\HttpFoundation\Request;
use Yosymfony\Silex\ConfigServiceProvider\ConfigServiceProvider;
use Yosymfony\Toml\Toml;

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

	/**
	 * @var ViewStub[]
	 */
	private $views = [];

	private $currentRoute = '';
	/**
	 * @var Module
	 */
	private $currentModule = null;

	private $staticViewIds = [];

	function __construct(array $values = array()) {
		parent::__construct($values);
		
		$this->register(new SilexProvider\UrlGeneratorServiceProvider());

		$appSettings = Toml::parse(CONFIG_DIR.'/app.toml');
		if (isset($appSettings['assets_path']))
			define("ASSETS_DIR", APP_ROOT_DIR.$appSettings['assets_path']);
		if (isset($appSettings['modules_path']))
			define("MODULES_DIR", APP_ROOT_DIR.$appSettings['modules_path']);

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

		if (isset($this['env']['memcache.enabled']) && $this['env']['memcache.enabled'] == 'true')
			$this->register(new MemcachedProvider());

		$this->before(function(Request $req){
			$this->setRouteName($req->get('_route'));
		});

		$staticViewIds = \Yosymfony\Toml\Toml::parse(APP_ROOT_DIR.'/config/static.toml');
		foreach ($staticViewIds as $id => $s) {
			$this->staticViewIds[] = $id;
		}
	}

	/**
	 * @param $id
	 * @param $type
	 * @param $r
	 * @param callable $f
	 * @return \Silex\Controller
	 */
	public function registerView($id, $type, $r, \Closure $f) {
		$this->views[$id] = new ViewStub($id, $type, ltrim($r, '/'), $f);
		$this->views[$id]->setApp($this);

		if (in_array($id, $this->staticViewIds))
			$this->views[$id]->setStatic(true);

		return $this->match($r, $f)->bind($id);
	}

	/**
	 * @param $id
	 * @return ViewStub
	 */
	public function getView($id) {
		if (isset($this->views[$id]))
			return $this->views[$id];

		return null;
	}

	/**
	 * @return ViewStub[]
	 */
	public function getViews() {
		return $this->views;
	}

	public function getCurrentModule() {
		return $this->currentModule;
	}

	public function setCurrentModule(Module $m) {
		$this->currentModule = $m;
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

	public function getWebPath() {
		return isset($this['env']['app.web_path']) ? $this['env']['app.web_path'] : '';
	}

	public function getAssetsPath() {
		return $this->getWebPath().$this['env']['app.assets_path'];
	}

	public function getStaticXMLPath() {
		return $this->getWebPath().$this['env']['app.static_xml_path'];
	}
}
