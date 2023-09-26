<?php

namespace Bondar\Roistat;

use Bondar\Config;
use Bondar\Utils\LoggerTrait;
use GuzzleHttp\Client;

class Api
{
    use LoggerTrait;

    const REQUEST_PING = 250001;
    const BASE_URI = 'https://cloud.roistat.com/api/v1/';

    private $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client(['base_uri' => self::BASE_URI]);

        $this->setLogger();
    }

    public function addEntities(string $entityName, array $entities)
    {
        switch ($entityName) {
            case 'order':
                $this->addOrders($entities);
                break;

            case 'client':
                $this->addClients($entities);
                break;

            default:
                break;
        }
    }

    public function addOrders(array $orders)
    {
        $this->request('project/add-orders', json_encode($orders));
    }

    public function addClients(array $clients)
    {
        $this->request('project/clients/import', json_encode($clients));
    }

    public function setStatuses(array $statuses)
    {
        $this->request('project/set-statuses', json_encode($statuses));
    }

    private function request($method, $body = '')
    {
        usleep(self::REQUEST_PING);

        try {
            $params = [
                'key' => Config::ROISTAT_API_KEY,
                'project' => Config::ROISTAT_PROJECT_ID,
            ];
            $uri = $method . '?' . http_build_query($params);
            $options = [
                'body' => $body,
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
            ];

            $response = $this->httpClient->request('POST', $uri, $options);
            $body = $response->getBody()->getContents();

            return json_decode($body, true);
        } finally {
            $this->log('info', [
                'Data' => [
                    '$method' => $method,
                    '$options' => $options,
                    '$responseBody' => $body,
                ],
            ]);
        }
    }
}