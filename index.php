<?php

namespace Ignite;

require_once __DIR__.'/vendor/autoload.php';
date_default_timezone_set('Europe/Bucharest');

EnvironmentManager::init();

$app = new Application();
$app['debug'] = true;
Localization::setLocale('en_US');

$m = new Modules\Homepage\Homepage($app);
$app->mount('/', $m);

$app->run();

?>