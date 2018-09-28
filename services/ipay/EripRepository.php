<?php

namespace app\services\ipay;

use app\models\payment\AccountPayment;
use yii\db\ActiveRecord;

class EripRepository
{
    /* @var  ActiveRecord $_modelClassName */
    protected $_modelClassName;
    protected $_model;

    public function __construct(AccountPayment $model)
    {
        $this->_model = $model;
        $this->_modelClassName = get_class($this->_model);

    }


    public function getOrderById(int $id, $asArray = true)
    {

        $query = $this->_modelClassName::find()
            ->where(['id' => $id]);
        if ($asArray) {
            $query->asArray(true);
        }

        $order = $query->one();

        return $this->convertOrder($order);
    }

    private function convertOrder($order)
    {
        if ($order) {
            $order['money'] = \Yii::$app->formatter->asDecimal($order['money'], 2,
                [\NumberFormatter::MIN_FRACTION_DIGITS => 0,]);
        }

        return $order;
    }

    public function changeStatusById(int $id, $status)
    {
        $this->_modelClassName::updateAll(['status' => $status], ['id' => $id]);
    }

    public function changeProcessById(int $id, int $status)
    {
        $this->_modelClassName::updateAll(['status_process' => $status], ['id' => $id]);
    }

    public function removeOrderById(int $id)
    {
        $this->_modelClassName::deleteAll(['id' => $id]);
    }

}