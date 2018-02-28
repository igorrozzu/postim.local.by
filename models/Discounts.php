<?php

namespace app\models;

use app\models\entities\DiscountOrder;
use app\models\entities\FavoritesDiscount;
use app\models\entities\GalleryDiscount;
use app\models\entities\OwnerPost;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\Json;

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
 * @property integer $count_favorites
 * @property string $url_name
 * @property integer $user_id
 * @property integer $count_orders
 * @property integer $promocode
 * @property string $price_with_discount
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
        'inactive' => 1,
        'editingAfterHiding' => 20,
        'active' => 21,
        'moderation' => 22,
        'editing' => 23,
    ];

    public $photos;
    public $requisites;
    public $isLike = false;

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
            [['description', 'key_word', 'title', 'promocode'], 'trim'],
            [['post_id', 'header', 'number_purchases', 'total_view_id', 'status', 'date_start',
                'date_finish', 'type', 'conditions', 'count_favorites', 'url_name', 'user_id', 'count_orders',
                'requisites'], 'required'],
            ['cover', 'required',
                'message' => 'Необходимо добавить хотя бы одну фотографию в галерею.'],
            [['post_id', 'total_view_id', 'status', 'date_start',
                'date_finish', 'type', 'count_favorites', 'user_id', 'count_orders'], 'integer'],
            [['data', 'header', 'cover', 'conditions', 'title', 'description', 'key_word',
                'url_name', 'promocode', 'requisites'], 'string'],
            [['price', 'price_with_discount'], 'number', 'min' => 0],
            ['discount', 'number', 'min' => 0, 'max' => 100],
            ['number_purchases', 'integer', 'min' => 1],
            ['date_finish', 'validateDateFinish'],
            ['type', 'in', 'range' => array_values(self::TYPE)],
            ['status', 'in', 'range' => array_values(self::STATUS)],
            [['post_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Posts::className(), 'targetAttribute' => ['post_id' => 'id']],
            [['total_view_id'], 'exist', 'skipOnError' => true,
                'targetClass' => TotalView::className(), 'targetAttribute' => ['total_view_id' => 'id']],
            ['number_purchases', 'validateNumberPurchases'],
            [['requisites'], 'safe'],
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
            'price_with_discount' => 'Цена со скидкой',
            'number_purchases' => 'Количество промокодов',
            'discount' => 'Скидка',
            'total_view_id' => 'Total View ID',
            'status' => 'Status',
            'date_start' => 'Date Start',
            'date_finish' => 'Дата окончания акции',
            'type' => 'Категория',
            'conditions' => 'Условия акции',
            'requisites' => 'Реквизиты',
        ];
    }

    public function behaviors()
    {
        return [
            'slug' => [
                'class' => 'app\behaviors\Slug',
                'in_attribute' => 'header',
                'out_attribute' => 'url_name',
            ],
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

    public function validateNumberPurchases($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if ($this->number_purchases <= $this->count_orders) {
                $this->addError($attribute, 'Количество промокодов должно быть больше заказов');
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
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
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
    public function getFavoritesDiscount()
    {
        return $this->hasMany(FavoritesDiscount::className(), ['discount_id' => 'id']);
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGallery()
    {
        return $this->hasMany(GalleryDiscount::className(), ['discount_id' => 'id']);
    }

    public function getHasLike()
    {
        return $this->hasOne(FavoritesDiscount::className(), ['discount_id' => 'id'])
            ->onCondition([FavoritesDiscount::tableName() . '.user_id' => Yii::$app->user->id]);
    }

    public function getTotalComments(){
        return $this->hasMany(Comments::className(), ['entity_id' => 'id'])->where(['type_entity' => Comments::TYPE['discount']])->count();
    }


    /**
     * @return string
     */
    public function getCover(): string
    {
        return '/discount_photo/' . $this->post_id . '/' . $this->cover;
    }

    /**
     * @return string
     * @param string $pictureName
     */
    public function getPathToPicture(string $pictureName): string
    {
        return '/discount_photo/' . $this->post_id . '/' . $pictureName;
    }

    public function getNameType(): string
    {
        switch ($this->type)
        {
            case self::TYPE['promoCode']: return 'Промокод';
            case self::TYPE['certificate']: return 'Сертификат';
            default: return '';
        }
    }

    public function edit()
    {
        $result = false;
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $this->encodeProperties();
            if ($this->update() && $this->addPhotos() && $this->editPhotos()) {
                $result = true;
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
            $hasCover = false;

            foreach ($this->photos as $link => $photo) {
                $arr = [];
                $arr['link'] = $link;
                $arr['src'] = $photo['src'];
                $arr['confirm'] = $photo['confirm'];

                if ($arr['confirm'] == 'true') {
                    $this->cover = $link;
                    $hasCover = true;
                }
               $photos[] = $arr;
            }
            $this->photos = $photos;

            if (!$hasCover) {
                $this->cover = !empty($photos) ? $photos[0]['link'] : null;
            }
        } else {
            $this->cover = null;
        }

        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }

    public function encodeProperties()
    {
        $encodedConditions = [];
        if (isset($_POST['discount']['conditions'])) {
            foreach ($this->conditions as $condition) {
                $condition = trim($condition);
                if (!empty($condition)) {
                    $encodedConditions[] = Html::encode($condition);
                }
            }
        }
        $this->conditions = Json::encode($encodedConditions);

        $this->requisites = Html::encode($this->requisites);
        $this->promocode = Html::encode($this->promocode);
        $this->header = Html::encode($this->header);
        $this->title = Html::encode($this->title);
        $this->description = Html::encode($this->description);
        $this->key_word = Html::encode($this->key_word);
    }

    public function addPhotos(): bool
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
                                    'status' => GalleryDiscount::STATUS['active'],
                                    'source' => $photo['src'],
                                ];
                                unlink($tmpLink);
                            }
                        }
                    }

                    if (empty($rows)) {
                        return true;
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

    public function editPhotos()
    {
        try {
            $discountPhotos = GalleryDiscount::find()
                ->where(['discount_id' => $this->id])
                ->all();

            foreach ($discountPhotos as $discountPhoto){
                $isset = false;

                if ($this->photos && is_array($this->photos)) {
                    foreach ($this->photos as $photo) {
                        if ($photo['link'] === $discountPhoto['link']) {
                            if ($photo['src'] !== $discountPhoto->source) {
                                $discountPhoto->source = $photo['src'];
                                $discountPhoto->save();
                            }
                            $isset = true;
                            break;
                        }
                    }
                }

                if (!$isset) {
                    $tmpLink = Yii::getAlias('@webroot/' . $this->getPathToPicture($discountPhoto['link']));

                    if (file_exists($tmpLink)) {
                        unlink($tmpLink);
                    }
                    $discountPhoto->delete();
                }
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    public function calculateEconomy()
    {
        if (isset($this->price) && isset($this->price_with_discount) ) {
            $economy = $this->price - $this->price_with_discount;

            if ($economy > 0) {
                return round($economy, 2);
            }
        }

        return null;
    }

    public function afterFind()
    {
        if ($this->isRelationPopulated('favoritesDiscount') ||
            ($this->isRelationPopulated('hasLike') && !empty($this->hasLike))
        ) {
            $this->isLike = true;
        }

        parent::afterFind(); // TODO: Change the autogenerated stub
    }
}