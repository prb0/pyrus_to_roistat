<?php

use Bondar\Logger;
use Bondar\PyrusToRoistat\MyCRM;

require_once __DIR__ . '/vendor/autoload.php';

try {
    echo (new MyCRM($_REQUEST))->getResponse();
} catch (Exception $e) {
    $logger = new Logger();
    $logger->log('error', [
        $e
    ]);
}
