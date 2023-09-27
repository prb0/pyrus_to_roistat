<?php

namespace Bondar\Utils;

use GuzzleHttp\Client;

abstract class AbstractApi
{
    use LoggerTrait;

    protected $httpClient;
    protected static $requestPing;
    protected static $baseUri;

    public function __construct()
    {
        $this->httpClient = new Client(['base_uri' => static::$baseUri]);

        $this->setLogger(static::class);
    }
}
