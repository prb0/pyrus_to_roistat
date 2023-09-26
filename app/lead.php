<?php

use Bondar\PyrusToRoistat\MyCRM;
use Bondar\Utils\Logger;

require_once __DIR__ . '/vendor/autoload.php';

try {
    echo (new MyCRM($_REQUEST))->getResponse();
} catch (Exception $e) {
    $logger = new Logger('Lead');
    $logger->log('error', [
        $e
    ]);
}
