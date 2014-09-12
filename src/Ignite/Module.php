<?php
namespace Ignite;

use Silex\Application as SilexApp;
use Silex\ControllerProviderInterface;
use Yosymfony\Toml\Toml;

abstract class Module implements ControllerProviderInterface {

	private $moduleName = '';

	abstract public function views(Application $app);

	public function __construct(Application $app) {
		$this->moduleName = (new \ReflectionClass($this))->getShortName();
		$app->parsedLayout = Toml::parse(MODULES_DIR.'/'.$this->moduleName.'/config.toml');
	}

    public function connect(SilexApp $app) {
		$app->setModuleName($this->moduleName);

		return $this->views($app);
    }
    
    public static function configurationDirectory() {
	    return __DIR__."/config";
    }
}
?>