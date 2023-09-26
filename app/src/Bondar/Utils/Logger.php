<?php

namespace Bondar\Utils;

class Logger
{
    use LoggerTrait;

    public function __construct()
    {
        $this->setLogger();
    }
}