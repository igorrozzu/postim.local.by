<?php

namespace app\models;

use app\models\entities\Gallery;
use app\models\moderation_post\PostModerationFeatures;
use app\models\moderation_post\PostModerationInfo;
use app\models\moderation_post\PostModerationUnderCategory;
use app\models\moderation_post\PostsModeration;
use app\models\moderation_post\WorkingHoursModeration;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Html;


class AddPost extends Model
{
    public $name,
        $article, $address_text,
        $comments_to_address, $coords_address, $requisites, $metro, $id;

    public $categories, $city, $contacts,
        $time_work, $features, $photos, $engine, $editors;

    private $cover = null;

    public static $SCENARIO_ADD = 'add';
    public static $SCENARIO_EDIT_MODERATOR = 'edit-moderator';
    public static $SCENARIO_EDIT_USER = 'edit-user';
    public static $SCENARIO_EDIT_USER_SELF_POST = 'edit-user-self-post';

    private $PostUnderCategory;
    private $PostFeatures;
    private $PostInfo;
    private $WorkingHours;
    private $Posts;

    public $customError = false;


    private function initScenario()
    {
        $mainScenario = [
            self::$SCENARIO_EDIT_MODERATOR,
            self::$SCENARIO_EDIT_USER_SELF_POST,
            self::$SCENARIO_ADD,
        ];

        if (in_array($this->getScenario(), $mainScenario)) {
            $this->PostUnderCategory = PostUnderCategory::className();
            $this->PostFeatures = PostFeatures::className();
            $this->PostInfo = PostInfo::className();
            $this->WorkingHours = WorkingHours::className();
            $this->Posts = Posts::className();
        } else {
            $this->PostUnderCategory = PostModerationUnderCategory::className();
            $this->PostFeatures = PostModerationFeatures::className();
            $this->PostInfo = PostModerationInfo::className();
            $this->WorkingHours = WorkingHoursModeration::className();
            $this->Posts = PostsModeration::className();
        }
    }

    public function setScenario($value)
    {
        parent::setScenario($value);
        $this->initScenario();
    }

    public function rules()
    {
        return [
            [
                ['name', 'coords_address', 'address_text', 'categories', 'time_work', 'city', 'id'],
                'required',
                'message' => 'Поле обязательно для заполнения',
            ],
            [['name', 'address_text'], 'match', 'pattern' => '/^\S.{3,}/i'],
            [
                ['metro', 'requisites', 'article', 'comments_to_address', 'contacts', 'features', 'photos', 'engine'],
                'safe',
            ],
        ];
    }

    public function scenarios()
    {
        return [
            self::$SCENARIO_ADD => [
                'metro',
                'requisites',
                'name',
                'photos',
                'engine',
                'coords_address',
                'address_text',
                'categories',
                'time_work',
                'city',
                'article',
                'comments_to_address',
                'contacts',
                'features',
            ],
            self::$SCENARIO_EDIT_USER => [
                'metro',
                'requisites',
                'editors',
                'id',
                'name',
                'photos',
                'engine',
                'coords_address',
                'address_text',
                'categories',
                'time_work',
                'city',
                'article',
                'comments_to_address',
                'contacts',
                'features',
            ],
            self::$SCENARIO_EDIT_MODERATOR => [
                'metro',
                'requisites',
                'editors',
                'id',
                'name',
                'photos',
                'engine',
                'coords_address',
                'address_text',
                'categories',
                'time_work',
                'city',
                'article',
                'comments_to_address',
                'contacts',
                'features',
            ],
            self::$SCENARIO_EDIT_USER_SELF_POST => [
                'metro',
                'requisites',
                'editors',
                'id',
                'name',
                'photos',
                'engine',
                'coords_address',
                'address_text',
                'categories',
                'time_work',
                'city',
                'article',
                'comments_to_address',
                'contacts',
                'features',
            ],
        ];
    }

    public function save()
    {

        if ($this->validate()) {

            switch ($this->getScenario()) {
                case self::$SCENARIO_ADD:
                    $this->addPost();
                    break;
                case self::$SCENARIO_EDIT_MODERATOR:
                    $this->editPost();
                    break;
                case self::$SCENARIO_EDIT_USER_SELF_POST:
                    $this->editPost();
                    break;
                case self::$SCENARIO_EDIT_USER:
                    $this->editPostUser();
                    break;
            }

        }

        self::updateCountUserPlace(Yii::$app->user->getId());
        return true;
    }

    public function addPost(int $main_id = 0)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $total_view = new TotalView(['count' => 0]);
            if ($total_view->save()) {

                $post = new $this->Posts();
                $post->city_id = $this->city;

                $latLng = explode(',', $this->coords_address);
                $post->lat = $latLng[0];
                $post->lon = $latLng[1];

                $post->rating = 0;
                $post->data = Html::encode($this->name);
                $post->address = Html::encode($this->address_text);
                $post->additional_address = Html::encode($this->comments_to_address);
                $post->user_id = Yii::$app->user->getId();
                $post->status = Yii::$app->user->identity->role > 1 ? 1 : 0;
                $post->date = time();
                $post->total_view_id = $total_view->id;
                $post->count_favorites = 0;
                $post->count_reviews = 0;
                $post->priority = 0;
                $post->metro = $this->metro;
                $post->requisites = Html::encode($this->requisites);

                $oldPost = null;
                $oldPostInfo = null;
                if ($main_id != 0) {
                    $post->main_id = $main_id;
                    $oldPost = Posts::find()->with('info')->where(['id' => $main_id])->one();
                    $post->url_name = $oldPost->url_name;
                    $oldPostInfo = $oldPost->info;
                }

                if (Yii::$app->user->identity->role > 1) {
                    $post->title = Html::encode($this->engine['title']);
                    $post->description = Html::encode($this->engine['description']);
                    $post->key_word = Html::encode($this->engine['key_word']);
                }


                for ($i = 1; $i < 8; $i++) {
                    if ($this->time_work[$i]['finish'] > 0) {
                        $post->priority = 1;
                        break;
                    }
                }

                if ($post->save()) {

                    $this->addCategories($post->id);
                    $this->addFeatures($post->id);
                    $this->addPostInfo($post->id, $oldPostInfo);
                    $this->addWorkTime($post->id);

                    if ($this->getScenario() != self::$SCENARIO_EDIT_USER) {
                        $this->addPhotos($post->id);
                    }

                    if ($this->cover) {
                        $post->cover = '/post_photo/' . $post->id . '/' . $this->cover;
                        $post->update();
                    } elseif ($oldPost != null) {
                        $post->cover = $oldPost->cover;
                        $post->update();
                    }
                } else {
                    $this->customError = true;
                }

            } else {
                $this->customError = true;
            }
        } catch (Exception $exception) {
            $this->customError = true;
        }

        if ($this->customError) {
            $transaction->rollBack();
        } else {
            $transaction->commit();
        }
    }

    public function editPost($post = null)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {

            $old_post_info = null;

            if ($post == null) {
                $post = Posts::find()
                    ->with(['postCategory', 'info'])
                    ->where(['id' => $this->id])->one();
                $old_post_info = $post->info;
            }

            $this->removeRelations($post);

            $post->city_id = $this->city;

            $latLng = explode(',', $this->coords_address);
            $post->lat = $latLng[0];
            $post->lon = $latLng[1];

            $post->data = Html::encode($this->name);
            $post->address = Html::encode($this->address_text);
            $post->additional_address = Html::encode($this->comments_to_address);
            $post->priority = 0;
            $post->requisites = Html::encode($this->requisites);
            $post->metro = $this->metro;

            if (Yii::$app->user->identity->role > 1) {
                $post->title = Html::encode($this->engine['title']);
                $post->description = Html::encode($this->engine['description']);
                $post->key_word = Html::encode($this->engine['key_word']);
            }

            for ($i = 1; $i < 8; $i++) {
                if ($this->time_work[$i]['finish'] > 0) {
                    $post->priority = 1;
                    break;
                }
            }

            if ($post->update()) {
                $this->addCategories($post->id);
                $this->addFeatures($post->id);
                $this->addPostInfo($post->id, $old_post_info);
                $this->addWorkTime($post->id);

                if ($this->getScenario() != self::$SCENARIO_EDIT_USER) {
                    $this->addPhotos($post->id);
                    $this->editPhotos($post->id);
                }

                if ($this->cover) {
                    $post->cover = '/post_photo/' . $post->id . '/' . $this->cover;
                    $post->update();
                }
            } else {
                $this->customError = true;
            }

        } catch (Exception $exception) {
            $this->customError = true;
        }

        if ($this->customError) {
            $transaction->rollBack();
        } else {
            $transaction->commit();
        }

    }

    public function editPostUser()
    {
        $oldPost = PostsModeration::find()->with(['postCategory', 'info'])->where([
            'main_id' => $this->id,
            'user_id' => Yii::$app->user->getId(),
        ])->one();
        if ($oldPost != null) {
            $this->editPost($oldPost);
        } else {
            $this->addPost($this->id);
        }

    }


    public function beforeValidate()
    {


        if (is_array($this->time_work)) {
            $newWorkTime = [];
            if ($this->time_work['btns'] == 'unselect' || $this->time_work['btns'] == 'select') {
                $timeStart = 0;
                $timeFinish = $this->time_work['btns'] == 'unselect' ? null : 86400;
                for ($i = 1; $i < 8; $i++) {
                    if ($timeFinish == null) {
                        $timeStart = $timeFinish;
                    }
                    $newWorkTime[$i] = ['start' => $timeStart, 'finish' => $timeFinish];
                }
            } else {
                for ($i = 1; $i < 8; $i++) {

                    $timeStart = isset($this->time_work[$i]['start']) && $this->time_work[$i]['start'] ? $this->getTimeByTextTime($this->time_work[$i]['start']) : 0;
                    $timeFinish = isset($this->time_work[$i]['finish']) && $this->time_work[$i]['finish'] ? $this->getTimeByTextTime($this->time_work[$i]['finish']) : 0;

                    if ($timeFinish <= $timeStart) {
                        $timeFinish += (24 * 3600);
                    }

                    if (!$this->time_work[$i]['start'] || !$this->time_work[$i]['finish']) {
                        $timeFinish = $timeStart = null;
                    }

                    $newWorkTime[$i] = ['start' => $timeStart, 'finish' => $timeFinish];
                }
            }

            $this->time_work = $newWorkTime;
        }

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
                array_push($photos, $arr);
            }
            $this->photos = $photos;
        }

        if (isset($this->features) && $this->features) {
            foreach ($this->features as $key => $feature) {
                if (!is_array($feature) && !((int)$feature)) {
                    unset($this->features[$key]);
                }
            }
        }

        return parent::beforeValidate();
    }


    private function addCategories(int $post_id)
    {
        $priority = 1;

        foreach ($this->categories as $category) {
            $post_under_category = new $this->PostUnderCategory([
                    'post_id' => $post_id,
                    'under_category_id' => $category,
                    'priority' => $priority === 1 ? $priority++ : 0,
                ]
            );
            if (!$post_under_category->save()) {
                $this->customError = true;
            }
        }
    }

    private function addFeatures(int $post_id)
    {

        if ($this->features && is_array($this->features)) {
            foreach ($this->features as $idFeature => $feature) {
                if (!is_array($feature)) {
                    $feature = (double)str_replace(',', '.', $feature);
                    $post_feature = new $this->PostFeatures([
                        'features_id' => $idFeature,
                        'post_id' => $post_id,
                        'value' => $feature,
                    ]);
                    if (!$post_feature->save()) {
                        $this->customError = true;
                    }
                } elseif ($idFeature == 'additionally') {
                    foreach ($feature as $additionally) {
                        $post_feature = new $this->PostFeatures([
                            'features_id' => $additionally,
                            'post_id' => $post_id,
                            'value' => 1,
                        ]);
                        if (!$post_feature->save()) {
                            $this->customError = true;
                        }
                    }
                } else {
                    $post_main_feature = new $this->PostFeatures([
                        'features_id' => $idFeature,
                        'post_id' => $post_id,
                        'value' => 1,
                    ]);
                    if (!$post_main_feature->save()) {
                        $this->customError = true;
                    }
                    foreach ($feature as $item) {
                        $post_feature = new $this->PostFeatures([
                            'features_id' => $item,
                            'post_id' => $post_id,
                            'value' => 1,
                            'features_main_id' => $idFeature,
                        ]);
                        if (!$post_feature->save()) {
                            $this->customError = true;
                        }
                    }
                }
            }
        }
    }

    private function addPostInfo(int $post_id, $old_post_info = null)
    {
        $postInfo = new $this->PostInfo();

        if ($this->contacts && is_array($this->contacts)) {
            foreach ($this->contacts as $key => $contact) {
                if ($key == 'phones') {
                    $postInfo->phones = $contact;
                }

                if ($key == 'web_site') {
                    $postInfo->web_site = $contact;
                }
                if ($key == 'social_networks') {
                    $postInfo->social_networks = $contact;
                }

            }
        }

        $postInfo->article = $this->article ? $this->article : null;

        if ($postInfo->editors && !in_array(Yii::$app->user->getId(), $postInfo->editors)) {

            $arr = $postInfo->editors;
            array_push($arr, Yii::$app->user->getId());
            $postInfo->editors = $arr;

        } elseif ($old_post_info) {
            $postInfo->editors = $old_post_info->editors;
            if (!in_array(Yii::$app->user->getId(), $old_post_info->editors)) {

                $arr = $postInfo->editors;
                array_push($arr, Yii::$app->user->getId());
                $postInfo->editors = $arr;

            }

        } else {
            if ($this->editors) {
                $postInfo->editors = $this->editors;
                if (is_array($postInfo->editors)) {
                    $arr = $postInfo->editors;
                } else {
                    $arr = [];
                }
                if (!in_array(Yii::$app->user->getId(), $postInfo->editors)) {
                    array_push($arr, Yii::$app->user->getId());
                    $postInfo->editors = $arr;
                }

            } else {
                $arr = [];
                array_push($arr, Yii::$app->user->getId());
                $postInfo->editors = $arr;
            }


        }
        $postInfo->post_id = $post_id;

        if (!$postInfo->save()) {
            $this->customError = true;
        }

    }

    private function addWorkTime(int $post_id)
    {

        foreach ($this->time_work as $index => $item) {
            if ($item['start'] !== null) {
                $workingHours = new $this->WorkingHours();
                $workingHours->day_type = $index;
                $workingHours->time_start = $item['start'];
                $workingHours->time_finish = $item['finish'];
                $workingHours->post_id = $post_id;
                if (!$workingHours->save()) {
                    $this->customError = true;
                }
            }
        }
    }

    private function addPhotos(int $post_id)
    {

        if ($this->photos) {
            $dir = Yii::getAlias('@webroot/post_photo/' . $post_id . '/');
            if (!is_dir($dir)) {
                FileHelper::createDirectory($dir);
            }
            if ($this->photos && is_array($this->photos)) {
                foreach ($this->photos as $photo) {

                    $tmpLink = Yii::getAlias('@webroot/post_photo/tmp/' . $photo['link']);
                    if (file_exists($tmpLink)) {
                        if (copy($tmpLink, $dir . $photo['link'])) {
                            $photoStatus = Yii::$app->user->isModerator() ?
                                Gallery::$STATUS['confirm'] : Gallery::$STATUS['moderation'];

                            $gallery = new Gallery([
                                'post_id' => $post_id,
                                'user_id' => Yii::$app->user->getId(),
                                'link' => $photo['link'],
                                'user_status' => Gallery::USER_STATUS['owner'],
                                'status' => $photoStatus,
                                'date' => time(),
                                'source' => $photo['src'],
                            ]);

                            if (!$gallery->save()) {
                                $this->customError = true;
                            }

                            unlink($tmpLink);
                        }
                    }
                }
            }

        }
    }

    private function editPhotos(int $pos_id)
    {
        $galleries = Gallery::find()
            ->where([
                'post_id' => $pos_id,
                'user_status' => Gallery::USER_STATUS['owner'],
            ])->all();

        if ($galleries && is_array($galleries)) {
            foreach ($galleries as $gallery) {
                $is_isset = false;
                if ($this->photos && is_array($this->photos)) {
                    foreach ($this->photos as $photo) {
                        if ($photo['link'] == $gallery['link']) {
                            if ($photo['src'] !== $gallery->source) {
                                $gallery->source = $photo['src'];
                                $gallery->save();
                            }
                            $is_isset = true;
                            break;
                        }
                    }
                }

                if (!$is_isset) {
                    $tmpLink = Yii::getAlias('@webroot/post_photo/' . $pos_id . '/' . $gallery['link']);
                    if (file_exists($tmpLink)) {
                        unlink($tmpLink);
                    }
                    $gallery->delete();
                }
            }
        }
    }

    private function removeRelations($post)
    {

        foreach ($post->postCategory as $item) {
            $item->delete();
        }
        ($this->WorkingHours)::deleteAll(['post_id' => $post->id]);
        ($this->PostFeatures)::deleteAll(['post_id' => $post->id]);
        if ($post->info) {
            $post->info->delete();
        }

    }

    private function getTimeByTextTime($textTime)
    {
        $currentTimestamp = Yii::$app->formatter->asTimestamp($textTime);
        $currentTime = idate('H', $currentTimestamp) * 3600 + idate('i', $currentTimestamp) * 60 + idate('s',
                $currentTimestamp);
        return $currentTime;
    }

    public static function updateCountUserPlace($user_id)
    {
        $user = UserInfo::find()->where(['user_id' => $user_id])->one();

        $count = Posts::find()
            ->joinWith('info')
            ->where("editors @> '[" . $user_id . "]'")
            ->andWhere(['status' => Posts::$STATUS['confirm']])
            ->count();

        $countModeration = PostsModeration::find()->where([
            'user_id' => $user_id,
            'status' => Posts::$STATUS['moderation'],
        ])->count();
        $countModeration2 = Posts::find()->where([
            'user_id' => $user_id,
            'status' => Posts::$STATUS['moderation'],
        ])->count();

        $user->count_places_added = $count;
        $user->count_place_moderation = $countModeration + $countModeration2;
        $user->update();

    }


    public static function countCategorySave($post)
    {


        if ($post->status == 1) {
            foreach ($post->categories as $under_category) {

                self::savePostCategoryCount($under_category->url_name,
                    $post->city->name,
                    $post->city->url_name
                );
                self::savePostCategoryCount($under_category->url_name,
                    $post->city->region->name,
                    $post->city->region->url_name
                );
                self::savePostCategoryCount($under_category->url_name,
                    $post->city->region->coutries->name,
                    $post->city->region->coutries->url_name
                );

                self::savePostCategoryCount($under_category->category->url_name,
                    $post->city->name,
                    $post->city->url_name
                );

                self::savePostCategoryCount($under_category->category->url_name,
                    $post->city->region->name,
                    $post->city->region->url_name
                );

                self::savePostCategoryCount($under_category->category->url_name,
                    $post->city->region->coutries->name,
                    $post->city->region->coutries->url_name
                );
            }
        }

    }

    public static function savePostCategoryCount($category_name, $city_name, $city_url_name)
    {

        $model = PostCategoryCount::find()
            ->where(['category_url_name' => $category_name])
            ->andWhere(['city_name' => $city_name])->one();
        if ($model != null) {

            $query = Posts::find()
                ->joinWith('categories.category')
                ->joinWith('city.region')
                ->where(['status' => 1]);
            if (\Yii::$app->category->getCategoryByName($category_name)) {
                $query->andWhere(['tbl_category.url_name' => $category_name]);
            } else {
                $query->andWhere(['tbl_under_category.url_name' => $category_name]);
            }
            if ($city_name != 'Беларусь') {
                $query->andWhere([
                    'or',
                    ['tbl_region.url_name' => $city_url_name],
                    ['tbl_city.url_name' => $city_url_name],
                ]);
            }
            $query->groupBy('tbl_posts.id');
            $count = $query->count();
            $model->count = $count;
            $model->update();
        } else {
            $model = new PostCategoryCount([
                'category_url_name' => $category_name,
                'city_name' => $city_name,
                'count' => -1,
            ]);
            $model->save();
        }
    }


}

