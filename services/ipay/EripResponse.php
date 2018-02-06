<?php

namespace app\services\ipay;

class EripResponse extends AEripResponse {


    private $templates = [
        'error' => '<?xml version="1.0" encoding="windows-1251"?><ServiceProvider_Response><Error><ErrorLine>{{message}}</ErrorLine></Error></ServiceProvider_Response>',
        'info'  => '<?xml version="1.0" encoding="windows-1251"?><ServiceProvider_Response><ServiceInfo><Amount><Debt>{{money}}</Debt><Penalty/></Amount><Info xml:space="preserve"><InfoLine>Номер заказа {{id}}: </InfoLine><InfoLine>Пополнение счета</InfoLine></Info></ServiceInfo></ServiceProvider_Response>',
        'storn' => '<?xml version="1.0" encoding="windows-1251"?><ServiceProvider_Response></ServiceProvider_Response>',
        'transaction_result' => '<?xml version="1.0" encoding="windows-1251"?><ServiceProvider_Response><TransactionResult><Info xml:space="preserve"><InfoLine>{{message}}</InfoLine></Info></TransactionResult></ServiceProvider_Response>',
        'transaction_start'  => '<?xml version="1.0" encoding="windows-1251"?><ServiceProvider_Response><TransactionStart><ServiceProvider_TrxId>{{id}}</ServiceProvider_TrxId><Info xml:space="preserve"><InfoLine>Заказ {{id}}:</InfoLine><InfoLine>Пополнение счета</InfoLine></Info></TransactionStart></ServiceProvider_Response>'
    ];


    public function getResponse(string $type, array $data): string
    {

        $response = '';

        if(isset($this->templates[$type]))
        {
            $template = $this->templates[$type];
            preg_match_all('/(?<={{)\w+(?=}})/', $template, $attributes);

            if(isset($attributes[0])){
                $attributes = $attributes[0];
                foreach ($attributes as $attribute){
                    if($data[$attribute] ?? false){
                        $template = str_replace("{{{$attribute}}}", $data[$attribute], $template);
                    }
                }
            }
            $response = $template;
        }

        return mb_convert_encoding($response, 'utf-8', 'windows-1251');

    }

}