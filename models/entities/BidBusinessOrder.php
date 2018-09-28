<?php

namespace app\models\entities;

use app\models\Posts;
use app\models\User;
use Yii;

/**
 * This is the model class for table "tbl_bid_business_order".
 *
 * @property string $position
 * @property integer $status
 * @property integer $type
 * @property integer $date
 * @property string $full_name
 * @property string $company_name
 * @property string $email
 *
 */
class BidBusinessOrder extends \yii\db\ActiveRecord
{
    private $mapStatusText = [
        10 => 'Бизнес-аккаунт',
        20 => 'Бизнес-аккаунт премиум',
        1 => 'Проверено',
        0 => 'Не проверено',
    ];

    public static $BIZ_ORDER = 10;
    public static $BIZ_PREMIUM_ORDER = 10;
    public static $VERIFIED = 1;
    public static $NOT_VERIFIED = 0;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_bid_business_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'type'], 'required'],
            [['position'], 'required', 'message' => 'Введите должность'],
            [['email'], 'required', 'message' => 'Введите адрес электронной почты'],
            [['email'], 'email', 'message' => 'Введите корректный адрес электронной почты'],
            [['full_name'], 'required', 'message' => 'Введите имя и фамилию'],
            [['company_name'], 'required', 'message' => 'Введите название компании'],
            [['phone'], 'required', 'message' => 'Введите телефон'],
            [['position', 'phone'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'position' => 'Должность',
            'phone' => 'Телефон',
            'status' => 'Статус',
            'type' => 'Тип заявки',
            'company_name' => 'Название компании',
            'full_name' => 'Имя и фамилия',
        ];
    }

    public function getStatusText(int $status)
    {

        return $this->mapStatusText[$status] ?? '';
    }

    public function beforeValidate()
    {
        if (!$this->date) {
            $this->date = time();
        }

        if (!$this->type) {
            $this->type = self::$BIZ_PREMIUM_ORDER;
        }

        if (!$this->status) {
            $this->status = self::$NOT_VERIFIED;
        }

        return parent::beforeValidate();
    }

    public function getButtons()
    {

        if ($this->status === self::$NOT_VERIFIED) {
            return "<div class='data-grid-container-btn'>
                        <a title='Одобрить' href='/admin/biz/change-status-order?id={$this->id}&action=confirm' class='btn-moderation --confirm'></a>
                        <a title='Удалить' href='/admin/biz/change-status-order?id={$this->id}&action=delete' class='btn-moderation --delete'></a>
                </div>";
        }

        return "<div class='data-grid-container-btn'>
                        <a title='Удалить' href='/admin/biz/change-status-order?id={$this->id}&action=delete' class='btn-moderation --delete'></a>
                </div>";

    }

}