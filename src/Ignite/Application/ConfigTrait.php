<?php

namespace Ignite\Application;

use Yosymfony\Silex\ConfigServiceProvider\Config;

trait ConfigTrait
{
    public function scan($path)
    {
        return $this['configuration']->load($path);
    }
}