<?php

namespace app\models\moderation_post;

use app\components\Helper;
use app\models\City;
use app\models\entities\FavoritesPost;
use app\models\entities\Gallery;
use app\models\TotalView;
use app\models\UnderCategory;
use app\models\User;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tbl_posts_moderation".
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
class PostsModeration extends \yii\db\ActiveRecord
{

    public $is_open = false;
    public $current_working_hours = null;
    public $timeOpenOrClosed = null;

    public $lat;
    public $lon;
    public $distance = null;
    public $distanceText = null;

    public static $STATUS = [
        'moderation' => 0,
        'confirm' => 1,
        'private' => 2,
    ];


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_posts_moderation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url_name', 'city_id', 'rating', 'data', 'total_view_id'], 'required'],
            [['url_name', 'cover', 'data', 'address'], 'string'],
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
        ];
    }

    public function behaviors()
    {
        return [
            'OpenPlace' => [
                'class' => 'app\behaviors\OpenPlace',
                'only_is_open' => false,
            ],
            'slug' => [
                'class' => 'app\behaviors\Slug',
                'in_attribute' => 'data',
                'out_attribute' => 'url_name',
            ],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getPostUnderCategory()
    {
        return $this->hasMany(PostModerationUnderCategory::className(), ['post_id' => 'id']);
    }

    public function getPostCategory()
    {
        return $this->hasMany(PostModerationUnderCategory::className(), ['post_id' => 'id']);
    }

    public function getCategories()
    {
        return $this->hasMany(UnderCategory::className(), ['id' => 'under_category_id'])
            ->via('postUnderCategory');
    }

    public function getOnlyOnceCategories()
    {
        return $this->hasMany(UnderCategory::className(), ['id' => 'under_category_id'])
            ->via('postUnderCategory', function ($query) {
                $query->orderBy(['priority' => SORT_DESC])
                    ->limit(1);
            });
    }

    public function getCategoriesPriority()
    {
        $postUnderCategory = UnderCategory::find()
            ->where(['post_id' => $this->id])
            ->innerJoin(PostModerationUnderCategory::tableName(), 'under_category_id = tbl_under_category.id')
            ->orderBy(['priority' => SORT_DESC])
            ->asArray()
            ->all();
        return $postUnderCategory;
    }


    public function getPostFeatures()
    {
        return $this->hasMany(PostModerationFeatures::className(), ['post_id' => 'id']);
    }


    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }


    public function getInfo()
    {
        return $this->hasOne(PostModerationInfo::className(), ['post_id' => 'id']);
    }

    public function getWorkingHours()
    {
        return $this->hasMany(WorkingHoursModeration::className(), ['post_id' => 'id']);
    }

    public function getTotalView()
    {
        return $this->hasOne(TotalView::className(), ['id' => 'total_view_id']);
    }


    public function afterFind()
    {
        parent::afterFind();


        if ($this->coordinates) {
            $latLng = explode(',', $this->coordinates);
            $this->lat = str_replace('(', '', $latLng[0]);
            $this->lon = str_replace(')', '', $latLng[1]);
        }
        if (isset(Yii::$app->request->cookies)) {
            if ($this->distance == null && $CurrentMePosition = Yii::$app->request->cookies->getValue('geolocation') ? \yii\helpers\Json::decode(Yii::$app->request->cookies->getValue('geolocation')) : false) {
                if ($this->lat && $this->lon) {
                    $this->distanceText = Yii::$app->oldFormatter->asShortLength(Helper::getDistanceBP($this->lat,
                        $this->lon,
                        $CurrentMePosition['lat'], $CurrentMePosition['lon']));
                }
            }
        }

        if ($this->distance) {
            $this->distanceText = Yii::$app->oldFormatter->asShortLength((int)$this->distance);
        }

    }

    public function beforeValidate()
    {
        $this->coordinates = '(' . $this->lat . ',' . $this->lon . ')';
        return parent::beforeValidate();
    }

    public function getFeatures()
    {
        $features = Yii::$app->db->createCommand('SELECT tbl_post_moderation_features.value,tbl_post_moderation_features.features_id, tbl_features.name,tbl_features.type
                  FROM tbl_post_moderation_features
                  INNER JOIN tbl_features ON tbl_post_moderation_features.features_id = tbl_features.id
                  WHERE tbl_post_moderation_features.post_id=:post_id AND features_main_id is null
                  ')
            ->bindValue('post_id', $this->id)
            ->queryAll();

        $features_result = new \stdClass();
        $features_result->rubrics = [];
        $features_result->features = [];

        foreach ($features as $feature) {
            if ($feature['type'] == 3) {
                $featureTypeArray = $feature;
                $under_features = Yii::$app->db->createCommand('SELECT tbl_post_moderation_features.value, tbl_features.name,tbl_features.type
                  FROM tbl_post_moderation_features
                  INNER JOIN tbl_features ON tbl_post_moderation_features.features_id = tbl_features.id
                  WHERE tbl_post_moderation_features.post_id=:post_id AND features_main_id=:main_id
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
