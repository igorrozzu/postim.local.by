<?php

namespace app\models\entities;

use app\models\Discounts;
use Yii;

/**
 * This is the model class for table "tbl_gallery_discount".
 *
 * @property integer $discount_id
 * @property string $link
 * @property string $source
 * @property integer $status
 * @property integer $id
 *
 * @property Discounts $discount
 */
class GalleryDiscount extends \yii\db\ActiveRecord
{
    const STATUS = [
        'inactive' => 0,
        'active' => 1,
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_gallery_discount';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['discount_id', 'link', 'status'], 'required'],
            [['discount_id', 'status'], 'integer'],
            [['link'], 'string'],
            [['source'], 'string', 'max' => 400],
            [['discount_id'], 'exist', 'skipOnError' => true, 'targetClass' => Discounts::className(), 'targetAttribute' => ['discount_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'discount_id' => 'Discount ID',
            'link' => 'Link',
            'source' => 'Source',
            'status' => 'Status',
            'id' => 'ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiscount()
    {
        return $this->hasOne(Discounts::className(), ['id' => 'discount_id']);
    }
}
