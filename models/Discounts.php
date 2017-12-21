<?php

namespace app\models;

use app\models\entities\DiscountOrder;
use app\models\entities\GalleryDiscount;
use app\models\entities\OwnerPost;
use Yii;
use yii\db\Exception;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "tbl_discounts".
 *
 * @property integer $id
 * @property integer $post_id
 * @property string $data
 * @property string $header
 * @property string $cover
 * @property string $price
 * @property integer $number_purchases
 * @property string $discount
 * @property integer $total_view_id
 * @property integer $status
 * @property integer $date_start
 * @property integer $date_finish
 * @property integer $type
 * @property string $conditions
 * @property string $title
 * @property string $description
 * @property string $key_word
 *
 * @property Posts $post
 * @property TotalView $totalView
 */
class Discounts extends \yii\db\ActiveRecord
{
    const TYPE = [
        'promoCode' => 1,
        'certificate' => 2
    ];
    const STATUS = [
        'active' => 1,
        'inactive' => 0
    ];

    public $photos;

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
            [['post_id', 'data', 'header', 'cover', 'number_purchases', 'discount', 'total_view_id',
                'status', 'date_start', 'date_finish', 'type', 'conditions'], 'required'],
            [['post_id', 'total_view_id', 'status', 'date_start', 'date_finish', 'type'], 'integer'],
            [['data', 'header', 'cover', 'conditions', 'title', 'description', 'key_word'], 'string'],
            ['price', 'number', 'min' => 0],
            ['number_purchases', 'integer', 'min' => 1],
            ['discount', 'number', 'min' => 0, 'max' => 100],
            ['date_finish', 'validateDateFinish'],
            ['type', 'in', 'range' => array_values(self::TYPE)],
            ['status', 'in', 'range' => array_values(self::STATUS)],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Posts::className(), 'targetAttribute' => ['post_id' => 'id']],
            [['total_view_id'], 'exist', 'skipOnError' => true, 'targetClass' => TotalView::className(), 'targetAttribute' => ['total_view_id' => 'id']],
            [['photos'], 'safe']
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
            'data' => 'Описание акции',
            'header' => 'Название скидки',
            'cover' => 'Cover',
            'price' => 'Стоимость товара или услуги',
            'number_purchases' => 'Колличество промокодов',
            'discount' => 'Скидка',
            'total_view_id' => 'Total View ID',
            'status' => 'Status',
            'date_start' => 'Date Start',
            'date_finish' => 'Дата окончания акции',
            'type' => 'Категория',
            'conditions' => 'Условия акции',
        ];
    }

    public function validateDateFinish($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if ($this->date_finish <= $this->date_start) {
                $this->addError($attribute, 'Неверная "Дата окончания акции"');
            }
        }
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

    public function create()
    {
        $result = false;
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $totalView = new TotalView(['count' => 0]);
            if ($totalView->save()) {
                $this->total_view_id = $totalView->id;

                if ($this->save() && $this->addPhotos()) {
                    $result = true;
                }
            }

        } catch (Exception $e){}

        if ($result) {
            $transaction->commit();
        } else {
            $transaction->rollBack();
        }

        return $result;
    }

    public function beforeValidate()
    {
        if (is_array($this->photos)) {
            $photos = [];
            foreach ($this->photos as $link => $photo) {
                $arr = [];
                $arr['link'] = $link;
                $arr['src'] = $photo['src'];
                $arr['confirm'] = $photo['confirm'];

                if ($arr['confirm'] == 'true') {
                    $this->cover = $link;
                }
               $photos[] = $arr;
            }
            $this->photos = $photos;
        }
        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }

    private function addPhotos(): bool
    {
        try {
            if ($this->photos) {
                $dir = Yii::getAlias('@webroot/discount_photo/' . $this->post_id . '/');

                if (!is_dir($dir)) {
                    FileHelper::createDirectory($dir);
                }

                $rows = [];
                if ($this->photos && is_array($this->photos)) {
                    foreach ($this->photos as $photo) {

                        $tmpLink = Yii::getAlias('@webroot/discount_photo/tmp/' . $photo['link']);
                        if (file_exists($tmpLink)) {
                            if (copy($tmpLink, $dir . $photo['link'])) {
                                $rows[] = [
                                    'discount_id' => $this->id,
                                    'link' => $photo['link'],
                                    'status' => 0,
                                    'source' => $photo['src'],
                                ];
                                unlink($tmpLink);
                            }
                        }
                    }

                    $result = Yii::$app->db->createCommand()
                        ->batchInsert(GalleryDiscount::tableName(), [
                            'discount_id',
                            'link',
                            'status',
                            'source'
                        ], $rows)
                        ->execute();

                    return $result > 0;
                }

            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }
}