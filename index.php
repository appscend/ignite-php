<?php
require_once __DIR__.'/vendor/autoload.php';
date_default_timezone_set('Europe/Bucharest');

$app = new Ignite\Application();
$app['debug'] = true;

$m = new Ignite\Modules\Homepage\Homepage($app);
$m->overwritePropsFromFile(APP_ROOT_DIR.'/test.toml');

$app->mount('/', $m);

$app->run();

?>