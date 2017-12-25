<?php
namespace app\components\discountOrder;

use app\models\Discounts;
use Yii;
use yii\base\Model;

/**
 * Created by PhpStorm.
 * User: igorrozu
 * Date: 12/25/17
 * Time: 3:41 PM
 */


abstract class Order
{
    protected $orderForm;

    protected $totalCost;

    protected $userInfo;

    protected $renderView;

    /**
     * Order constructor.
     * @param $orderForm
     */
    public function __construct($orderForm)
    {
        $this->orderForm = $orderForm;
        $this->userInfo = Yii::$app->user->identity->userInfo;
    }

    abstract public function createOrder(): bool;
    abstract protected function makePayment(): bool;

    abstract protected function paymentByErip(): bool;
    abstract protected function paymentByCard(): bool;
    abstract protected function paymentByRealMoney(): bool;

    /**
     * @param Model $orderForm
     * @return Order|null
     */
    public static function createProviderByType(Model $orderForm): ? Order
    {
        switch ((int) $orderForm->discountType)
        {
            case Discounts::TYPE['promoCode']: return new PromoCodeOrder($orderForm);
            default: return null;
        }
    }

    /**
     * @return mixed
     */
    public function getRenderView(): ? string
    {
        return $this->renderView;
    }
}