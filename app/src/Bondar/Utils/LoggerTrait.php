<?php

namespace Bondar\Utils;

use Bondar\Config;
use DateTime;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

trait LoggerTrait
{
    private $logger;

    private function setLogger($loggerName = 'Pyrus')
    {
        $this->logger = new Logger($loggerName);
        $this->logger->pushHandler(new StreamHandler('php://stdout', Logger::ERROR));

        if (true === Config::DEBUG) {
            $dateFormat = "Y-m-d H:i:s";
            $output = "%datetime% > %level_name% > %message%";
            $formatter = new LineFormatter($output, $dateFormat, true);
            $stream = new StreamHandler(
                Config::LOG_DIR
                    . (new DateTime())->format('d_m_Y_')
                    . static::class,
                Logger::INFO);
            $stream->setFormatter($formatter);

            $this->logger->pushHandler($stream);
        }
    }

    public function log(string $method, array $logData)
    {
        $this->logger->$method(print_r($logData, true));
    }
}