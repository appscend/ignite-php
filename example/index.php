<?php

namespace Ignite;

require_once 'vendor/autoload.php';

//default environment is devel
EnvironmentManager::init();
Localization::setLocale('en_US');

$app = new Application();

$m = new Modules\Sample\Sample($app);
$app->mount('/', $m);

$app->run();
