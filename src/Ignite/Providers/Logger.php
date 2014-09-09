<?php

namespace Ignite\Providers;

use Gelf\Message;
use Psr\Log\LogLevel;
use Silex\Application as SilexApp;
use Silex\ServiceProviderInterface;
use Silex\Provider as SilexProvider;
use Ignite\Providers;

class Logger implements ServiceProviderInterface {

	//TODO: more than 1 active logger

	const MONOLOG = 0;
	const GELF = 1;

	const LOG_INFO = 0;
	const LOG_WARN = 1;
	const LOG_ERROR = 2;

	private $logger = self::MONOLOG;
	/**
	 * @var SilexApp
	 */
	private $app = null;

	/**
	 * @param SilexApp $app
	 */
	public function __construct(SilexApp $app) {
		if (!empty($app['env']->get('monolog')))
			$this->logger = self::MONOLOG;
		else if (!empty($app['env']->get('gelf')))
			$this->logger = self::GELF;

		$this->app = $app;
	}

	/**
	 * @param $message
	 * @param int $severity
	 */
	public function log($message, $severity = self::LOG_INFO) {
		switch ($severity) {
			case self::LOG_INFO: {
				if ($this->logger === self::MONOLOG)
					$this->app['monolog']->addInfo($message);
				else if ($this->logger === self::GELF) {
					$msg = new Message();
					$msg->setFullMessage($message);
					$msg->setFacility($this->app['env']['gelf.facility']);
					$msg->setLevel(LogLevel::INFO);
					$this->app['gelf']->getPublisher()->publish($msg);
				}

				break;
			}

			case self::LOG_WARN: {
				if ($this->logger === self::MONOLOG)
					$this->app['monolog']->addWarning($message);
				else if ($this->logger === self::GELF) {
					$msg = new Message();
					$msg->setFullMessage($message);
					$msg->setFacility($this->app['env']['gelf.facility']);
					$msg->setLevel(LogLevel::WARNING);
					$this->app['gelf']->getPublisher()->publish($msg);
				}

				break;
			}

			case self::LOG_ERROR: {
				if ($this->logger === self::MONOLOG)
					$this->app['monolog']->addError($message);
				else if ($this->logger === self::GELF) {
					$msg = new Message();
					$msg->setFullMessage($message);
					$msg->setFacility($this->app['env']['gelf.facility']);
					$msg->setLevel(LogLevel::ERROR);
					$this->app['gelf']->getPublisher()->publish($msg);
				}

				break;
			}
		}
	}

	/**
	 * Registers services on the given app.
	 *
	 * This method should only be used to configure services and parameters.
	 * It should not get services.
	 *
	 * @param SilexApp $app An Application instance
	 */
	public function register(SilexApp $app) {
		$app['ignite_logger'] = $this;
	}

	/**
	 * Bootstraps the application.
	 *
	 * This method is called after all services are registered
	 * and should be used for "dynamic" configuration (whenever
	 * a service must be requested).
	 */
	public function boot(SilexApp $app) {
		switch ($this->logger) {
			case self::MONOLOG: {
				$app->register(new SilexProvider\MonologServiceProvider(), $app['env']->get('monolog'));

				if (!file_exists(APP_ROOT_DIR.'/'.$app['env']['monolog.logfile']))
					touch(APP_ROOT_DIR.'/'.$app['env']['monolog.logfile']);

				break;
			}

			case self::GELF: {
				$app->register(new GELFServiceProvider());


				break;
			}
		}
	}
}