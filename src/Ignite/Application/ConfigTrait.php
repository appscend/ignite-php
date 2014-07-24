<?php

namespace Ignite\Application;

use Yosymfony\Silex\ConfigServiceProvider\ConfigRepository;

trait ConfigTrait
{
	/**
	 * @param string $path
	 * @return ConfigRepository
	 */
	public function scan($path) {
        return $this['configuration']->load($path);
    }
}