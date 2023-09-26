<?php

namespace Bondar\Utils;

class Logger
{
    use LoggerTrait;

    public function __construct($loggerName = 'Logger')
    {
        $this->setLogger($loggerName);
    }
}