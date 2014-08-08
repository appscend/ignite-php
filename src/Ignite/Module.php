<?php
namespace Ignite;

use Silex\Application as SilexApp;
use Silex\ControllerProviderInterface;

abstract class Module implements ControllerProviderInterface
{
	abstract public function views(Application $app);
	
    public function connect(SilexApp $app) {
		$className = (new \ReflectionClass($this))->getShortName();

		$app->setModuleName($className);
		return $this->views($app);
    }
    
    public static function configurationDirectory() {
	    return __DIR__."/config";
    }
}
?>