<?php

namespace AcsNet\Api\Logger\Handler;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Order extends StreamHandler
{
    public function __construct()
    {
        $logFile = BP . '/var/log/order_integration.log';
        parent::__construct($logFile, Logger::DEBUG);
    }

}
