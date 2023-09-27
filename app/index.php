<?php

date_default_timezone_set('Europe/Moscow');

use Bondar\PyrusToRoistat\CronExport;
use Bondar\PyrusToRoistat\MyCRM;
use Bondar\Utils\Logger;

require_once __DIR__ . '/vendor/autoload.php';

try {
    if (empty($_REQUEST['action'])) {
        (new CronExport())->process();
    } else {
        echo (new MyCRM($_REQUEST))->getResponse();
    }
} catch (Exception $e) {
    $logger = new Logger('Export');
    $logger->log('error', [
        $e
    ]);
}

