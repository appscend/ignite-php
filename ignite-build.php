<?php

copy('./vendor/appscend/ignite-php/Phakefile', './Phakefile');
echo `./vendor/appscend/ignite-php/ignite setup`;

chmod('.', 0755);
chown('.', 'www-data');

$composerJson = json_decode(file_get_contents('composer.json'), true);

$composerJson['autoload'] = ['psr-4' => ["Ignite\\Modules\\" => "modules/"]];
$composerJson['minimum-stability'] = 'dev';

file_put_contents('composer.json', json_encode($composerJson));
echo `composer dumpautoload`;