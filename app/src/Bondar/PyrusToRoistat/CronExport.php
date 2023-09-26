<?php

namespace Bondar\PyrusToRoistat;

use Bondar\Config;
use Bondar\Pyrus\Api as PyrusApi;
use Bondar\Roistat\Api as RoistatApi;
use DateTime;
use Exception;
use Throwable;

class CronExport
{
    private $roistatApi;
    private $pyrusApi;

    public function __construct()
    {
        $this->roistatApi = new RoistatApi();
        $this->pyrusApi = new PyrusApi();
    }

    public function process()
    {
        try {
            $this->processEntities('order');
            $this->processEntities('client');
            $this->setStatuses();
        } catch (Throwable $e) {
            throw new Exception($e);
        }
    }

    private function setStatuses()
    {
        $this->roistatApi->setStatuses(Config::$statuses);
    }

    private function getBasicFormsFilter(): array
    {
        return [
            'modified_after' => (new DateTime('-7 day'))->format('Y-m-d\TH:i:s\Z'),
            'include_archived' => 'y',
        ];
    }

    private function processEntities(string $entityName)
    {
        $const = "pyrus_{$entityName}_form_id";
        $response = $this->pyrusApi->getFormRegister(
            Config::$$const,
            $this->getBasicFormsFilter());

        try {
            if (!empty($response['tasks'])) {
                $this->putEntitiesToRoistat($entityName, $response['tasks']);
            }
        } catch (Throwable $e) {
            throw new Exception($e);
        }
    }

    private function putEntitiesToRoistat(string $entityName, array $entities)
    {
        try {
            $_entities = [];
            foreach ($entities as $entity) {
                $_entities[] = $this->getPreparedRoistatEntity($entityName, $entity);

                if (count($_entities) === 100) {
                    $this->roistatApi->addEntities($entityName, $_entities);
                    $_entities = [];
                }
            }
            if (!empty($_entities)) {
                $this->roistatApi->addEntities($entityName, $_entities);
            }
        } catch (Throwable $e) {
            throw new Exception($e);
        }
    }

    private function getPreparedRoistatEntity($entityName, array $entity): array
    {
        try {
            switch ($entityName) {
                case 'order':
                    return $this->getPreparedRoistatOrder($entity);

                case 'client':
                    return $this->getPreparedRoistatClient($entity);

                default:
                    return [];
            }
        } catch (Throwable $e) {
            throw new Exception($e);
        }
    }

    public function getPreparedRoistatClient(array $client): array
    {
        $fields = $this->getFieldsHashTable($client['fields']);

        return [
            'id' => (string)$client['id'],
            'fields' => $this->parseFields($client['fields']),
            'name' => $fields[Config::PYRUS_CLIENT_NAME_ID]['value'],
            'email' => $fields[Config::PYRUS_CLIENT_EMAIL_ID]['value'],
            'phone' => $fields[Config::PYRUS_CLIENT_PHONE_ID]['value'],
            'birth_date' => $fields[Config::PYRUS_CLIENT_BIRTH_DATE_ID]['value'],
        ];
    }

    public function getPreparedRoistatOrder(array $order): array
    {
        $fields = $this->getFieldsHashTable($order['fields']);

        try {
            return [
                'id' => (string)$order['id'],
                'date_create' => (new DateTime($order['create_date']))->getTimestamp(),
                'name' => 'Заявка',
                'fields' => $this->parseFields($order['fields']),
                'client_id' => empty($fields[Config::PYRUS_CLIENT_FIELD_ID]['value']['task_id'])
                    ? ''
                    : (string)$fields[Config::PYRUS_CLIENT_FIELD_ID]['value']['task_id'],
                'status' => empty($fields[Config::PYRUS_STATUS_FIELD_ID]['value']['choice_id'])
                    ? (string)Config::PYRUS_DEFAULT_STATUS_ID
                    : (string)$fields[Config::PYRUS_STATUS_FIELD_ID]['value']['choice_id'],
                'roistat' => empty($fields[Config::PYRUS_ROISTAT_FIELD_ID]['value'])
                    ? ''
                    : $fields[Config::PYRUS_ROISTAT_FIELD_ID]['value'],
                'price' => empty($fields[Config::PYRUS_PRICE_FIELD_ID]['value'])
                    ? '0'
                    : $fields[Config::PYRUS_PRICE_FIELD_ID]['value'],
            ];
        } catch (Throwable $e) {
            throw new Exception($e);
        }
    }

    private function parseFields(array $fields): array
    {
        $result = [];

        foreach ($fields as $field) {
            if (empty($field['value'])) {
                continue;
            }
            if (is_array($field['value'])) {
                if (!empty($field['value']['choice_names'])) {
                    $result[$field['name']] = implode(', ', $field['value']['choice_names']);
                } else if (!empty($field['value']['subject'])) {
                    $result[$field['name']] = $field['value']['subject'];
                }
            } else {
                $result[$field['name']] = $field['value'];
            }
        }

        return $result;
    }

    private function getFieldsHashTable(array $fields): array
    {
        $result = [];

        foreach ($fields as $field) {
            $result[$field['id']] = $field;
        }

        return $result;
    }
}
