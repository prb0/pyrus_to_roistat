<?php

namespace Bondar;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use DateTime;

trait LoggerTrait
{
    private $logger;
    private function setLogger()
    {
        $this->logger = new Logger('Pyrus');
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