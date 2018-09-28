<?php

namespace app\models\entities;

use app\models\Posts;
use app\models\User;
use Yii;

/**
 * This is the model class for table "tbl_business_order".
 *
 * @property integer $user_id
 * @property integer $post_id
 * @property string $position
 * @property integer $status
 * @property integer $premium_finish_date
 */
class BusinessOrder extends \yii\db\ActiveRecord
{
    private $mapStatusText = [
        10 => 'Бизнес-аккаунт',
        20 => 'Заявка на доблавление',
        30 => 'Премиум бизнес-аккаунт',
    ];

    public static $BIZ_AC = 10;
    public static $BIZ_ORDER = 20;
    public static $PREMIUM_BIZ_AC = 30;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_business_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'post_id', 'status'], 'required'],
            [['position'], 'required', 'message' => 'Введите должность'],
            [['full_name'], 'required', 'message' => 'Введите имя и фамилию'],
            [['phone'], 'required', 'message' => 'Введите телефон'],
            [['user_id', 'post_id', 'status', 'premium_finish_date'], 'integer'],
            [['position', 'phone'], 'string', 'max' => 100],
            [
                ['post_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Posts::className(),
                'targetAttribute' => ['post_id' => 'id'],
            ],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => ['user_id' => 'id'],
            ],
            [['user_id'], 'unique', 'targetAttribute' => ['user_id', 'post_id'], 'message' => 'Заявка уже отправлена'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'id user',
            'post_id' => 'id места',
            'position' => 'Должность',
            'phone' => 'Телефон',
            'status' => 'Статус',
            'premium_finish_date' => 'Оплачено до',
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

        return parent::beforeValidate();
    }

    public function getPost()
    {
        return $this->hasOne(Posts::className(), ['id' => 'post_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}