<?php

namespace Ignite\Providers;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Config\FileLocator;

class LocatorServiceProvider implements ServiceProviderInterface
{    
    public function register(Application $app)
    {
        $app['locator'] = function($app) {
            return new FileLocator($app['locator.directories']);
        };
    }
    
    public function boot(Application $app)
    {
    	$app['locator.directories'] = array();
    }
}