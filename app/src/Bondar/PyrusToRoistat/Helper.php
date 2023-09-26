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
        return stripos($id, 'choice') !== false
            || stripos($id, 'task') !== false;
    }

    public static function getFieldsHashTable(array $fields): array
    {
        $result = [];

        foreach ($fields as $field) {
            $result[$field['id']] = $field;
        }

        return $result;
    }

    public static function getClosedStatuses(): array
    {
        $closedStatuses = array_filter(Config::$statuses, function ($status) {
            return $status['type'] === 'paid' || $status['type'] === 'canceled';
        });

        foreach ($closedStatuses as $key => $status) {
            $closedStatuses[$key] = $status['id'];
        }

        return $closedStatuses;
    }
}