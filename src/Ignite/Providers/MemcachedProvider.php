<?php
namespace Ignite\Providers;

use Silex\Application;
use Silex\ServiceProviderInterface;

class MemcachedProvider implements ServiceProviderInterface {

	/**
	 * Registers services on the given app.
	 *
	 * This method should only be used to configure services and parameters.
	 * It should not get services.
	 *
	 * @param Application $app An Application instance
	 */
	public function register(Application $app) {
		$app['memcache'] = new \Memcached('ignite');
	}

	/**
	 * Bootstraps the application.
	 *
	 * This method is called after all services are registered
	 * and should be used for "dynamic" configuration (whenever
	 * a service must be requested).
	 */
	public function boot(Application $app) {
		$app['memcache']->addServer($app['env']['memcache.host'], $app['env']['memcache.port']);
	}


} 