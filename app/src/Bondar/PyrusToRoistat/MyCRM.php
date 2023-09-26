<?php

namespace Bondar\PyrusToRoistat;

use Bondar\Config;
use Bondar\Pyrus\Api;
use Exception;

class MyCRM
{
    private $pyrusApi;
    private $data;
    private $doubleLeadId = 0;

    public function __construct(array $request)
    {
        $this->pyrusApi = new Api();
        $this->data = $request;
        $this->parseProxyLeadFields();

        if (!$this->isValidRequest()) {
            throw new Exception('Wrong action');
        }
        if (!$this->isAuthenticated()) {
            throw new Exception('Wrong token');
        }
    }

    private function isAuthenticated(): bool
    {
        if ($this->data['token'] === Config::MY_CRM_TOKEN) {
            return true;
        }

        return false;
    }

    private function isValidRequest(): bool
    {
        if (empty($this->data['action'])) {
            return false;
        }

        return true;
    }

    public function getResponse(): string
    {
        switch ($this->data['action']) {
            case 'lead':
                $result = $this->addLead();
                break;

            default:
                throw new Exception('Wrong action');
        }

        return json_encode($result);
    }

    public function addLead(): array
    {
        $this->data['client_id'] = $this->getPyrusClient()['id'];

        if ($this->isDouble()) {
            return ['status' => 'ok', 'lead_id' => $this->doubleLeadId];
        }

        $leadData = $this->getPreparedPyrusOrder();
        $lead = $this->pyrusApi->postTasks([
            'form_id' => Config::$pyrus_order_form_id,
            'fields' => $leadData,
        ]);

        return $lead['task']['id']
            ? ['status' => 'ok', 'lead_id' => $lead['task']['id']]
            : ['status' => 'failed', 'lead_id' => 'failed'];
    }

    private function isDouble(): bool
    {
        $response = $this->pyrusApi->getFormRegister(Config::$pyrus_order_form_id, [
            'fld' . Config::PYRUS_CLIENT_FIELD_ID => $this->data['client_id'],
        ]);

        if (!empty($response['tasks'])) {
            $this->doubleLeadId = $response['tasks'][0]['id'];
            return true;
        }

        return false;
    }

    private function getPyrusClient(): array
    {
        if (!empty($this->data['phone'])) {
            $response = $this->pyrusApi->getFormRegister(Config::$pyrus_client_form_id, [
                'fld' . Config::PYRUS_CLIENT_PHONE_ID => $this->data['phone'],
            ]);

            if (!empty($response['tasks'])) {
                return $response['tasks'][0];
            }
        }
        if (!empty($this->data['email'])) {
            $response = $this->pyrusApi->getFormRegister(Config::$pyrus_client_form_id, [
                'fld' . Config::PYRUS_CLIENT_PHONE_ID => $this->data['email'],
            ]);

            if (!empty($response['tasks'])) {
                return $response['tasks'][0];
            }
        }

        return $this->pyrusApi->postTasks([
            'form_id' => Config::$pyrus_client_form_id,
            'fields' => $this->prepareBasicClientFields(),
        ])['task'];
    }

    private function getPreparedPyrusOrder(): array
    {
        $result = $this->prepareBasicOrderFields();

        foreach ($this->data['data'] as $id => $value) {
            if (!is_numeric($id)) {
                continue;
            }
            $result[] = [
                'id' => $id,
                'value' => $value,
            ];
        }

        return $result;
    }

    private function prepareBasicClientFields(): array
    {
        return [
            [
                'id' => Config::PYRUS_CLIENT_NAME_ID,
                'value' => $this->data['name'] ?? '',
            ], [
                'id' => Config::PYRUS_CLIENT_PHONE_ID,
                'value' => $this->data['phone'] ?? '',
            ], [
                'id' => Config::PYRUS_CLIENT_EMAIL_ID,
                'value' => $this->data['email'] ?? '',
            ],
        ];
    }

    private function prepareBasicOrderFields(): array
    {
        return [
            [
                'id' => Config::PYRUS_STATUS_FIELD_ID,
                'value' => [
                    'choice_id' => Config::PYRUS_DEFAULT_STATUS_ID
                ],
            ], [
                'id' => Config::PYRUS_CLIENT_FIELD_ID,
                'value' =>  [
                    'task_id' => $this->data['client_id']
                ],
            ], [
                'id' => Config::PYRUS_ROISTAT_FIELD_ID,
                'value' => $this->data['visit'] ?? '',
            ],
        ];
    }

    public function parseProxyLeadFields()
    {
        $this->data['data'] = !empty($this->data['data'])
            ? json_decode($this->data['data'], true)
            : [];
    }
}