<?php

namespace app\models;

use app\models\entities\DiscountOrder;
use app\models\entities\OwnerPost;
use Yii;

/**
 * This is the model class for table "tbl_discounts".
 *
 * @property integer $id
 * @property integer $post_id
 * @property string $data
 * @property string $header
 * @property string $cover
 * @property double $price
 * @property integer $number_purchases
 * @property double $price_promo
 * @property double $discount
 * @property integer $total_view_id
 * @property integer $status
 * @property integer $date_start
 * @property integer $date_finish
 *
 * @property Posts $post
 * @property TotalView $totalView
 * @property DiscountOrder[] $tblUsersPromos
 */
class Discounts extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_discounts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['post_id', 'data', 'header', 'cover', 'price', 'number_purchases', 'price_promo', 'discount', 'total_view_id', 'status', 'date_start', 'date_finish', 'type'], 'required'],
            [['post_id', 'number_purchases', 'total_view_id', 'status', 'date_start', 'date_finish', 'type'], 'integer'],
            [['data', 'header', 'cover'], 'string'],
            [['price', 'price_promo', 'discount'], 'number'],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Posts::className(), 'targetAttribute' => ['post_id' => 'id']],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => OwnerPost::className(), 'targetAttribute' => ['post_id' => 'post_id']],
            [['total_view_id'], 'exist', 'skipOnError' => true, 'targetClass' => TotalView::className(), 'targetAttribute' => ['total_view_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'post_id' => 'Post ID',
            'data' => 'Data',
            'header' => 'Header',
            'cover' => 'Cover',
            'price' => 'Price',
            'number_purchases' => 'Number Purchases',
            'price_promo' => 'Price Promo',
            'discount' => 'Discount',
            'total_view_id' => 'Total View ID',
            'status' => 'Status',
            'date_start' => 'Date Start',
            'date_finish' => 'Date Finish',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(Posts::className(), ['id' => 'post_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTotalView()
    {
        return $this->hasOne(TotalView::className(), ['id' => 'total_view_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiscountOrder()
    {
        return $this->hasMany(DiscountOrder::className(), ['discount_id' => 'id']);
    }

    public function getOwnerPost()
    {
        return $this->hasMany(OwnerPost::className(), ['post_id' => 'post_id']);
    }

    public function getOwners()
    {
        return $this->hasMany(User::className(), ['id' => 'owner_id'])
            ->via('ownerPost');
    }

    public function getSumOfPrice()
    {
        return self::find()->sum('price');
    }
}
