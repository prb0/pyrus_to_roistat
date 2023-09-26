<?php

use Bondar\PyrusToRoistat\CronExport;
use Bondar\Utils\Logger;

require_once __DIR__ . '/vendor/autoload.php';

try {
    (new CronExport())->process();
} catch (Exception $e) {
    $logger = new Logger('Export');
    $logger->log('error', [
        $e
    ]);
}

