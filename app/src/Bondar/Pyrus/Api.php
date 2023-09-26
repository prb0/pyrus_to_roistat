<?php

namespace Bondar\Pyrus;

use Bondar\Config;
use Bondar\Utils\LoggerTrait;
use GuzzleHttp\Client;

class Api
{
    use LoggerTrait;

    const REQUEST_PING = 150001;
    const BASE_URI = 'https://api.pyrus.com/v4/';
    private $token;
    private $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client(['base_uri' => self::BASE_URI]);

        $this->setLogger();
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
        usleep(self::REQUEST_PING);

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