<?php

namespace Bondar;

class Config
{
    const DEBUG = true;
    const LOG_DIR = __DIR__ . '/../../logs/';
    const MY_CRM_TOKEN = 'fff';
    const PYRUS_API_KEY = 'fff';
    const PYRUS_LOGIN = 'fffv@fff.fff';
    const PYRUS_ROISTAT_FIELD_ID = 21;
    const PYRUS_CLIENT_FIELD_ID = 20;
    const PYRUS_PRICE_FIELD_ID = 15;
    const PYRUS_STATUS_FIELD_ID = 19;
    const PYRUS_DEFAULT_STATUS_ID = 2;
    const PYRUS_CLIENT_NAME_ID = 5;
    const PYRUS_CLIENT_PHONE_ID = 6;
    const PYRUS_CLIENT_EMAIL_ID = 7;
    const PYRUS_ORDER_NAME_ID = 5;
    const PYRUS_ORDER_PHONE_ID = 6;
    const PYRUS_ORDER_EMAIL_ID = 7;
    const PYRUS_CLIENT_BIRTH_DATE_ID = 20;
    const ROISTAT_API_KEY = 'fff';
    const ROISTAT_PROJECT_ID = '111111';

    public static $pyrus_client_form_id = 1311524; // 1 more usage in $CronExport->processEntities()
    public static $pyrus_order_form_id = 1311523; // 1 more usage in $CronExport->processEntities()

    public static $statuses = [
        [
            'id' => '2',
            'name' => 'Лид',
            'type' => 'progress',
        ],
        [
            'id' => '5',
            'name' => 'Договор заключен',
            'type' => 'paid',
        ],
        [
            'id' => '7',
            'name' => 'Не целевая',
            'type' => 'canceled',
        ],
    ];
}