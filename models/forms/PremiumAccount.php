<?php

namespace app\models\forms;

use app\models\Posts;
use Yii;
use yii\base\Model;

/**
 * This is the model class for form "PremiumAccount".
 *
 * @property integer $type
 * @property string $money
 */
class PremiumAccount extends Model
{
    public $postId;
    public $rate;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['postId', 'rate'], 'required'],
            [['postId', 'rate'], 'integer'],
            ['rate', 'in', 'range' => array_keys(Yii::$app->params['premiumAccount']['rates'])],
            [
                ['postId'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Posts::className(),
                'targetAttribute' => ['postId' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'postId' => 'Название',
            'rate' => 'Период',
        ];
    }

    public function getRateInfo()
    {
        $rates = Yii::$app->params['premiumAccount']['rates'];

        return $rates[$this->rate];
    }
}
