<?php

namespace app\models\forms;

use app\models\Discounts;
use yii\base\Model;

/**
 * This is the model class for form "discount_order".
 *
 * @property integer $count
 * @property string $paymentType
 * @property integer $discountType
 */
class DiscountOrder extends Model
{
    const PAYMENT_TYPE = ['erip' => 1, 'card' => 2, 'virtual-money' => 3, 'mega-money' => 4];

    public $count;
    public $paymentType;
    public $discountType;

    public $discount;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['count', 'paymentType', 'discountType'], 'required'],
            [['paymentType', 'discountType'], 'integer'],
            ['count', 'integer', 'min' => 1],
            ['discountType', 'in', 'range' => array_values(Discounts::TYPE)],
            ['paymentType', 'in', 'range' => array_values(DiscountOrder::PAYMENT_TYPE)],
            ['count', 'checkPurchasesCount'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'count' => 'Колличество',
            'paymentType' => 'Cпособ оплаты',
            'discountType' => 'Тип скидки',
        ];
    }

    public function checkPurchasesCount($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $orderCount = \app\models\entities\DiscountOrder::find()
                ->where(['discount_id' => $this->discount->id])
                ->count();

            $lastPurchasesCount = $this->discount->number_purchases - $orderCount;

            if ($this->count > $lastPurchasesCount) {
                $this->addError($attribute,
                    "Колличество оставшихся скидок $lastPurchasesCount, а вы запросили $this->count");
            }
        }
    }
}
