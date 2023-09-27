<?php

namespace Bondar\Pyrus;

use Bondar\Config;
use Bondar\Utils\AbstractApi;

class Api extends AbstractApi
{
    protected $httpClient;
    protected static $requestPing = 150001;
    protected static $baseUri = 'https://api.pyrus.com/v4/';
    private $token;

    public function __construct()
    {
        parent::__construct();

        $this->auth();
    }

    public function getFormRegister(int $formId, array $filter)
    {
        return $this->request("forms/{$formId}/register", json_encode($filter));
    }

    public function postTasks(array $body)
    {
        return $this->request('tasks', json_encode($body));
    }

    private function auth()
    {
        try {
            $params = [
                'login' => Config::PYRUS_LOGIN,
                'security_key' => Config::PYRUS_API_KEY,
            ];
            $uri = 'auth?' . http_build_query($params);
            $response = $this->httpClient->request('GET', $uri);
            $body = $response->getBody()->getContents();
            $decoded = json_decode($body, true);
            $this->token = $decoded['access_token'];
        } finally {
            $this->log('info', [
                'Data' => [
                    '$uri' => $uri,
                    '$body' => @$body,
                ],
            ]);
        }
    }

    private function request($method, $body = '')
    {
        usleep(static::$requestPing);

        try {
            $options = [
                'body' => $body,
                'headers' => [
                    'Authorization' => "Bearer {$this->token}"
                ],
            ];

            if (empty($body)) {
                $httpMethod = 'GET';
            } else {
                $httpMethod = 'POST';
                $options['headers']['Content-Type'] = 'application/json';
            }

            $response = $this->httpClient->request($httpMethod, $method, $options);
            $body = (string)$response->getBody();

            return json_decode($body, true);
        } finally {
            $this->log('info', [
                'Data' => [
                    '$method' => $method,
                    '$httpMethod' => $httpMethod,
                    '$options' => $options,
                    '$body' => $body,
                ],
            ]);
        }
    }
}
