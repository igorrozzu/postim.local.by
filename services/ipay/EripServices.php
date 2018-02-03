<?php

namespace app\services\ipay;

use app\models\entities\Task;
use \domDocument;

class EripServices{

    /* @var  EripResponse $_responseImplementation */
    /* @var  EripRepository $_repositoryImplementation */

    protected $_responseImplementation      = null;
    protected $_repositoryImplementation    = null;

    const PROCESSED     = 1;
    const NOT_PROCESSED = 0;
    const UNPAID        = 0;
    const PAID          = 1;



    public function __construct(EripResponse $eripResponse, EripRepository $eripRepository)
    {
        $this->_responseImplementation   = $eripResponse;
        $this->_repositoryImplementation = $eripRepository;
    }

    public function processInfo(string $data)
    {
        $info           = $this->convertData($data);
        $orderNumber    = $info['PersonalAccount']['value'] ?? false;
        $idEripNumber   = $info['RequestId']['value'] ?? false;
        $result         = '';

        if($orderNumber && $idEripNumber){
            $order = $this->_repositoryImplementation->getOrderById($orderNumber);
            if($order){
                if($order['status'] == self::UNPAID){
                    if($order['status_process'] == self::NOT_PROCESSED){
                        $result = $this->_responseImplementation->getResponse(AEripResponse::$status['info'], $order);
                    }else{
                        $message['message'] = "Заказ N{$orderNumber} находится в процессе оплаты";
                        $result = $this->_responseImplementation->getResponse(AEripResponse::$status['error'], $message);
                    }
                }else{
                    $message['message'] = "Заказ N{$orderNumber} уже оплачен";
                    $result = $this->_responseImplementation->getResponse(AEripResponse::$status['error'], $message);
                }
            }else{
                $message['message'] = "Заказ N{$orderNumber} не существует. Начните оплату заново на сайте Postim.by";
                $result = $this->_responseImplementation->getResponse(AEripResponse::$status['error'], $message);
            }
        }

        return $this->signature($data, $result);
    }

    public function transactionStart(string $data)
    {

        $info           = $this->convertData($data);
        $orderNumber    = $info['PersonalAccount']['value'] ?? false;
        $result         = '';

        if($orderNumber){
            $order = $this->_repositoryImplementation->getOrderById($orderNumber);
            if($order){
                if($order['status_process'] == self::NOT_PROCESSED){
                    $this->_repositoryImplementation->changeProcessById($orderNumber, self::PROCESSED);
                    $result = $this->_responseImplementation->getResponse(AEripResponse::$status['transaction_start'], $order);
                }else{
                    $message['message'] = "Заказ N{$orderNumber} находится в процессе оплаты";
                    $result = $this->_responseImplementation->getResponse(AEripResponse::$status['error'], $message);
                }
            }else{
                $message['message'] = "Заказ N{$orderNumber} не существует. Начните оплату заново на сайте Postim.by";
                $result = $this->_responseImplementation->getResponse(AEripResponse::$status['error'], $message);
            }
        }

        return $this->signature($data, $result);
    }

    public function transactionResult(string $data){
        $info           = $this->convertData($data);
        $orderNumber    = $info['PersonalAccount']['value'] ?? false;
        $result         = '';
        $error          = $info['TransactionResult']['ErrorText'] ?? false;

        if($error === false){
            $message['message'] = "Оплата заказа {$orderNumber} успешно завершена.";
            $result = $this->_responseImplementation->getResponse(AEripResponse::$status['transaction_result'], $message);

            $order = $this->_repositoryImplementation->getOrderById($orderNumber);
            $this->_repositoryImplementation->changeStatusById($orderNumber, self::PAID);

            $task = new Task([
                'data' => json_encode([
                    'params' => [
                        'user_id' => $order->user_id,
                        'changing' => $order->money,
                    ],
                ]),
                'type' => Task::TYPE['accountReplenishment'],
            ]);
            $task->save();

        }else{
            $this->_repositoryImplementation->changeProcessById($orderNumber, self::NOT_PROCESSED);
            $result = $this->_responseImplementation->getResponse(AEripResponse::$status['transaction_result'], $error);
        }

        return $this->signature($data, $result);
    }

    public function stornStart(string $data)
    {
        $info           = $this->convertData($data);
        $orderNumber    = $info['PersonalAccount']['value'] ?? false;
        $result         = '';

        if($orderNumber) {
            $result = $this->_responseImplementation->getResponse(AEripResponse::$status['storn'], '');
        }

        return $this->signature($data, $result);
    }

    public function stornResult(string $data)
    {
        $info           = $this->convertData($data);
        $orderNumber    = $info['PersonalAccount']['value'] ?? false;
        $result         = '';

        if($orderNumber) {
            $order = $this->_repositoryImplementation->getOrderById($orderNumber);
            if($order){
                $result = $this->_responseImplementation->getResponse(AEripResponse::$status['storn'], '');
            }else{
                $message['message'] = "Заказ N{$orderNumber} не существует.";
                $result = $this->_responseImplementation->getResponse(AEripResponse::$status['error'], $message);
            }
        }

        return $this->signature($data, $result);
    }


    private function signature(string $inputXml, string $outputXml): string
    {

        $salt = addslashes('ncgdtyJUJJwq275ngFDRE');
        // Удаляем лишние символы до начала xml-запроса и после xml-запроса
        $XML = preg_replace('/^.*\<\?xml/sim', '<?xml', $inputXml);
        $XML = preg_replace('/\<\/ServiceProvider_Request\>.*/sim', '</ServiceProvider_Request>', $XML);
        // Избавляемся от экранирования
        $XML = stripslashes($XML);
        // Получаем подпись от iPay
        $signature = '';
        if (preg_match('/SALT\+MD5\:\s(.*)/', $_SERVER['HTTP_SERVICEPROVIDER_SIGNATURE'], $matches))
        {
            $signature = $matches[1];
        }
        // Проверяем подпись iPay
        if (strcasecmp(md5($salt.$XML), $signature)!=0)
        {
            // Формируем ответ с ошибкой проверки ЦП
            $body = '<?xml version="1.0" encoding="windows-1251"?><ServiceProvider_Response><Error><ErrorLine>Ошибка проверки ЦП</ErrorLine></Error></ServiceProvider_Response>';
            // Формируем ЦП и отправляем ЦП и ответ об ошибке в iPay
            $body = mb_convert_encoding('utf-8', 'windows-1251', $body);
            $md5 = md5($salt .$body);
            header("ServiceProvider-Signature: SALT+MD5: $md5");
            return $body;

        }
        $md5 = md5($salt . $outputXml);
        header("ServiceProvider-Signature: SALT+MD5: $md5");
    }


    private function convertData(string $data): array
    {
        $d = new domDocument('1.0', 'WINDOWS-1251');
        $d->LoadXML($data);
        $d->formatOutput = true;
        $xmlData = $d->saveXML();

        $result = [];
        $xml = simplexml_load_string($xmlData);
        /* @var /SimpleXMLElement $node */
        foreach ($xml as $key => $node) {
            $value = [];
            foreach ($node->attributes() as $k => $v) {
                $value[$k] = (string) $v;
            }
            foreach ($node->children() as $k =>$v){
                $value[$k] = (string) $v;
            }
            $value['value'] = (string) $node;
            $result[$key] = $value;
        }

        return $result;

    }

}