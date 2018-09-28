<?php

namespace app\services\ipay;

abstract class AEripResponse
{

    public static $status = [
        'info' => 'info',
        'transaction_start' => 'transaction_start',
        'transaction_result' => 'transaction_result',
        'storn' => 'storn',
        'error' => 'error',
    ];

    public function getResponse(string $type, array $data): string
    {

    }

}