<?php

namespace app\models\forms;

use yii\base\Model;

/**
 * This is the model class for form "AccountPayment".
 *
 * @property integer $type
 * @property string $money
 */
class AccountPayment extends Model
{
    const PAYMENT_TYPE = ['erip' => 1, 'card' => 2];

    public $type;
    public $money;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['money', 'type'], 'required'],
            [['type'], 'integer'],
            [['money'], 'number'],
            ['money', 'compare', 'compareValue' => 0, 'operator' => '>'],
            ['type', 'in', 'range' => array_values(AccountPayment::PAYMENT_TYPE)],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'money' => 'Сумма',
            'type' => 'Cпособ оплаты',
        ];
    }
}
