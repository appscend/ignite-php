<?php

namespace Ignite\Providers;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Gelf\Transport\UdpTransport;
use Gelf\Publisher;
use Gelf\Message;
use Gelf\Logger;

class GELFServiceProvider implements ServiceProviderInterface
{    
    public function register(Application $app)
    {
        $app['gelf'] = function($app) {
        	$publisher = new UdpTransport($app['gelf.domain'], $app['gelf.port'], UdpTransport::CHUNK_SIZE_LAN);
            return new Logger($publisher);
        };
    }
    
    public function boot(Application $app)
    {
    	$app['gelf.domain'] = "127.0.0.1";
    	$app['gelf.port'] = 12201;
    }
}