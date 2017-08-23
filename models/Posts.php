<?php

namespace app\models;

use app\components\Helper;
use app\models\entities\FavoritesPost;
use app\models\entities\Gallery;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tbl_posts".
 *
 * @property integer $id
 * @property string $url_name
 * @property string $latlon
 * @property integer $city_id
 * @property string $cover
 * @property integer $rating
 * @property string $data
 * @property string $address
 * @property integer $count_favorites
 * @property integer $count_reviews
 */
class Posts extends \yii\db\ActiveRecord
{

    public $is_like = false;
    public $is_open = false;
    public $current_working_hours = null;
    public $timeOpenOrClosed = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_posts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url_name', 'city_id', 'cover', 'rating', 'data', 'total_view_id'], 'required'],
            [['url_name', 'cover', 'data', 'address', 'latlon'], 'string'],
            [['city_id', 'rating', 'count_favorites', 'count_reviews'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'url_name' => 'Url Name',
            'city_id' => 'City ID',
            'under_category_id' => 'Under Category ID',
            'cover' => 'Cover',
            'rating' => 'Rating',
            'data' => 'Data',
            'address' => 'Address',
            'count_favorites' => 'Count Favorites',
            'count_reviews' => 'Count Reviews',
            'latlon' => 'Координаты'
        ];
    }

    public function behaviors()
    {
        return [
            'OpenPlace' => [
                'class' => 'app\behaviors\OpenPlace',
                'only_is_open' => false
            ],
            'slug' => [
                'class' => 'app\behaviors\Slug',
                'in_attribute' => 'data',
                'out_attribute' => 'url_name',
            ],
            'SaveJson' => [
                'class' => 'app\behaviors\SaveJson',
                'in_attributes' => ['latlon'],
            ]

        ];
    }


    public function getCategories()
    {
        return $this->hasMany(UnderCategory::className(), ['id' => 'under_category_id'])
            ->viaTable(PostUnderCategory::tableName(), ['post_id' => 'id']);
    }

    public function getPostFeatures()
    {
        return $this->hasMany(PostFeatures::className(), ['post_id' => 'id']);
    }


    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    public function getReviews()
    {
        return $this->hasMany(Reviews::className(), ['post_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFavoritePosts()
    {
        return $this->hasMany(FavoritesPost::className(), ['post_id' => 'id']);
    }

    public function getHasLike()
    {
        return $this->hasOne(FavoritesPost::className(), ['post_id' => 'id'])
            ->onCondition([FavoritesPost::tableName() . '.user_id' => Yii::$app->user->id]);

    }

    public function getInfo()
    {
        return $this->hasOne(PostInfo::className(), ['post_id' => 'id']);
    }

    public function getWorkingHours()
    {
        return $this->hasMany(WorkingHours::className(), ['post_id' => 'id']);
    }

    public function getTotalView()
    {
        return $this->hasOne(TotalView::className(), ['id' => 'total_view_id']);
    }

    public function getLastPhoto()
    {
        return $this->hasOne(Gallery::className(), ['post_id' => 'id'])
            ->select([Gallery::tableName().'.link', Gallery::tableName().'.post_id'])
            ->innerJoin('(SELECT tbl_c.post_id,MAX(tbl_c.id) as max_id
              FROM tbl_gallery tbl_c
              GROUP BY tbl_c.post_id) as max_record', 'max_record.post_id = '.Gallery::tableName().'.post_id')
            ->andWhere(Gallery::tableName().'.id = max_record.max_id');
    }

    public function afterFind()
    {
        parent::afterFind();

        if (strpos($this->cover, 'default') !== -1 && isset($this->lastPhoto)) {
            $this->cover = $this->lastPhoto->getPhotoPath();
        }

        if ($this->isRelationPopulated('favoritePosts') ||
            ($this->isRelationPopulated('hasLike') && !empty($this->hasLike))
        ) {
            $this->is_like = true;
        }
    }

    public function getFeatures()
    {
        $features = Yii::$app->db->createCommand('SELECT tbl_post_features.value,tbl_post_features.features_id, tbl_features.name,tbl_features.type
                  FROM tbl_post_features
                  INNER JOIN tbl_features ON tbl_post_features.features_id = tbl_features.id
                  WHERE tbl_post_features.post_id=:post_id AND features_main_id is null
                  ')
            ->bindValue('post_id', $this->id)
            ->queryAll();

        $features_result = new \stdClass();
        $features_result->rubrics = [];
        $features_result->features = [];

        foreach ($features as $feature) {
            if ($feature['type'] == 3) {
                $featureTypeArray = $feature;
                $under_features = Yii::$app->db->createCommand('SELECT tbl_post_features.value, tbl_features.name,tbl_features.type
                  FROM tbl_post_features
                  INNER JOIN tbl_features ON tbl_post_features.features_id = tbl_features.id
                  WHERE tbl_post_features.post_id=:post_id AND features_main_id=:main_id
                  ')
                    ->bindValue('post_id', $this->id)
                    ->bindValue('main_id', $feature['features_id'])
                    ->queryAll();
                foreach ($under_features as $under_feature) {
                    $featureTypeArray['values'][] = $under_feature;
                }
                array_push($features_result->rubrics, $featureTypeArray);

            } else {
                array_push($features_result->features, $feature);
            }
        }

        return $features_result;
    }
}
