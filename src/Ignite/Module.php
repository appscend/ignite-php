<?php
namespace Ignite;

use Silex\Application as SilexApp;
use Silex\ControllerProviderInterface;

abstract class Module implements ControllerProviderInterface
{
	abstract public function views(Application $app);
	
    public function connect(SilexApp $app)
    {
        return $this->views($app);
    }
    
    public static function configurationDirectory() {
	    return __DIR__."/config";
    }
}
?>