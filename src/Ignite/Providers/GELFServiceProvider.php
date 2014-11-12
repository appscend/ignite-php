<?php

namespace Ignite\Providers;

use Gelf\Publisher;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Gelf\Transport\UdpTransport;
use Gelf\Logger;

class GELFServiceProvider implements ServiceProviderInterface {

	public function register(Application $app) {
        $app['gelf'] = function($app) {
        	$transport = new UdpTransport($app['env']['gelf.domain'], $app['env']['gelf.port'], UdpTransport::CHUNK_SIZE_LAN);
			$publisher = new Publisher($transport);

            return new Logger($publisher, $app['env']['gelf.facility']);
        };
    }
    
    public function boot(Application $app)  {

    }
}