<?php

namespace Bondar;

class Logger
{
    use LoggerTrait;

    public function __construct()
    {
        $this->setLogger();
    }
}