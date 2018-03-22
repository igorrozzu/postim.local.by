<?php
namespace  app\models\sphinx;
use app\models\Discounts;
use yii\sphinx\ActiveRecord;

class Discount extends ActiveRecord
{
    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function indexName()
    {
        return 'discount_search';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiscount()
    {
        return $this->hasOne(Discounts::className(), ['id' => 'id']);
    }
}