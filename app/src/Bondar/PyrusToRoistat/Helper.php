<?php

namespace Bondar\PyrusToRoistat;

use Bondar\Config;

class Helper
{
    public static function parseFields(array $fields): array
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

    public static function isParseableField($id): bool
    {
        return stripos($id, '_id') !== false;
    }

    public static function parseField(array $fields): array
    {
        $re = '/^[a-zA-Z]*_id/m';
        preg_match_all($re, $fields[0], $type, PREG_SET_ORDER, 0);

        $re = '/\d*$/m';
        preg_match_all($re, $fields[0], $id, PREG_SET_ORDER, 0);

        return [
            'id' => (int) $id[0][0],
            'value' => [
                $type[0][0] => $fields[1]
            ],
        ];
    }

    public static function getFieldsHashTable(array $fields): array
    {
        $result = [];

        foreach ($fields as $field) {
            $result[$field['id']] = $field;
        }

        return $result;
    }

    public static function getActiveStatuses(): array
    {
        $closedStatuses = array_filter(Config::$statuses, function ($status) {
            return $status['type'] === 'progress';
        });

        foreach ($closedStatuses as $key => $status) {
            $closedStatuses[$key] = $status['id'];
        }

        return $closedStatuses;
    }
}