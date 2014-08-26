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
	 * a service must be requested).ls
	 */
	public function boot(Application $app) {
		if ($app['env']['memcache.type'] == 'local')
			$app['memcache']->addServer($app['env']['memcache.host'], $app['env']['memcache.port']);
		else if ($app['env']['memcache.type'] == 'elastic') {
			$app['memcache']->setOption(\Memcached::OPT_CLIENT_MODE, \Memcached::DYNAMIC_CLIENT_MODE);
			$app['memcache']->addServer($app['env']['memcache.host'], $app['env']['memcache.port']);
		}
	}


} 