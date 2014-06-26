<?php

namespace Ignite;

define("ROOT_DIR", dirname(dirname(__DIR__)));

use Silex\Application as SilexApp;
use Silex\Provider as SilexProvider;
use Yosymfony\Silex\ConfigServiceProvider\ConfigServiceProvider;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

class Application extends SilexApp implements ConfigurationInterface {
	use SilexApp\UrlGeneratorTrait;
	use SilexApp\MonologTrait;
	use Application\ConfigTrait;
	use Application\GELFTrait;
	
	const IGNITE_VERSION = '0.0.1';

	function __construct(array $values = array()) {
		parent::__construct($values);
		
		$this->register(new SilexProvider\MonologServiceProvider(), array(
		    'monolog.logfile' => ROOT_DIR.'/logs/development.log',
		));
		
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
		
		$appConfigData = array();
		try {$appConfigData = $this->scan("app.toml")->validateWith($this);}
			catch(\InvalidArgumentException $ex) {}		
		$this['config'] = $appConfigData;
	}
	
	public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root(0);
        
		$rootNode
			->children()
				->arrayNode('app')->ignoreExtraKeys()
					->children()
						->booleanNode('show_status_bar')->end()
					->end()
				->end()
			->end()
		->end();
        	
        return $treeBuilder;
    }
}
