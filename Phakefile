<?php

namespace Ignite;

use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;

chdir(realpath('../../../'));

require_once 'vendor/autoload.php';

EnvironmentManager::init();

\desc('Copies example files to app root to give a head start.');
\task('setup', function($args){
	if (isset($args['env']))
		EnvironmentManager::setEnvironment($args['env']);

	`cp -r -i example/* ../../../`;
});

\desc('Generates static xml files based on the configuration file.');
\task('generate_static', function($args) {
	if (isset($args['env']))
		EnvironmentManager::setEnvironment($args['env']);

	$app = new Application();

	if (isset($args['locale']))
		Localization::setLocale($args['locale']);

	if (isset($args['module'])) {
		$moduleNames = explode(',', $args['module']);

		foreach ($moduleNames as $class) {
			/**
			 * @var $m Module
			 */
			$m = (new \ReflectionClass('Ignite\Modules\\'.$class.'\\'.$class))->newInstanceArgs([$app]);
			$m->views($app);
			$m->generateStaticViews();
		}
	} else
		throw new MissingMandatoryParametersException('Missing \'module\' parameter.');
});