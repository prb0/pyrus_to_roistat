<?php

use Bondar\Logger;
use Bondar\PyrusToRoistat\CronExport;

require_once __DIR__ . '/vendor/autoload.php';

try {
    (new CronExport())->process();
} catch (Exception $e) {
    $logger = new Logger();
    $logger->log('error', [
        $e
    ]);
}

