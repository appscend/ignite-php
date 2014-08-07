<?php

namespace Ignite;

define("ROOT_DIR", dirname(dirname(__DIR__)));
define("MODULES_DIR", ROOT_DIR.'/modules');

use Silex\Application as SilexApp;
use Silex\Provider as SilexProvider;
use Yosymfony\Silex\ConfigServiceProvider\ConfigServiceProvider;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Application extends SilexApp {
	use SilexApp\UrlGeneratorTrait;
	use SilexApp\MonologTrait;
	use Application\ConfigTrait;
	use Application\GELFTrait;
	
	const IGNITE_VERSION = '0.0.1';

	function __construct(array $values = array()) {
		parent::__construct($values);
		
		$this->register(new SilexProvider\UrlGeneratorServiceProvider());
		
		$configurationPaths = array(ROOT_DIR, ROOT_DIR."/config");
		$modulePaths = glob(ROOT_DIR . "/modules" . '/*' , GLOB_ONLYDIR);
		foreach ($modulePaths as $modulePath)
			array_push($configurationPaths, $modulePath, $modulePath."/config");
		
		$this->register(new ConfigServiceProvider(
		    $configurationPaths
		));
		
		$this->register(new Providers\LocatorServiceProvider());
		$this['locator.directories'] = [ROOT_DIR];
		
		$this->register(new Providers\GELFServiceProvider());
		$this['gelf.domain'] = "logging.appscend.net";
		
		$this->register(new \Whoops\Provider\Silex\WhoopsServiceProvider);
		
		$this['dispatcher']->addSubscriber(new EventListener\ViewToResponseListener($this));

		$this->register(new EnvironmentManager());
		//$this['env']->setEnvironment('production');

		$this->register(new SilexProvider\MonologServiceProvider(), $this['env']->get('monolog'));
	}

	public function getAssetsPath() {
		return $this['env']['app.assets_path'];
	}

	public function getStaticXMLPath() {
		return $this['env']['app.static_xml_path'];
	}
}
