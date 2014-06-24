<?php

namespace Ignite\Application;

use Monolog\Logger;
use Monolog\Handler\GelfHandler;
use Gelf\Transport\UdpTransport;

trait GELFTrait
{
    public function gelf($message, array $context = array(), $level = Logger::INFO)
    {
        return $this['gelf']->log($level, $message, $context);
    }
}