<?php

namespace app\services\ipay;

class EripResponse extends AEripResponse {



    public function getResponse(string $type, array $data): string
    {

        $response = '';

        $filePath = \Yii::getAlias('@app/services/ipay/templates' . "/{$type}.template");

        if(file_exists($filePath)){

            $template = file_get_contents($filePath);

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

        if($response){
            return iconv('utf-8','windows-1251', trim($response));
        }

    }

}