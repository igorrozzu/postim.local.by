<?php
/**
 * Created by PhpStorm.
 * User: jrborisov
 * Date: 8.7.17
 * Time: 20.30
 */

namespace app\components\cardsPlaceWidget;

use app\models\City;
use app\models\moderation_post\PostModerationUnderCategory;
use app\models\moderation_post\WorkingHoursModeration;
use app\models\PostUnderCategory;
use app\models\UnderCategory;
use app\models\WorkingHours;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;


class CardsPlaceWidget extends Widget
{
    public $dataprovider;
    public $settings;

    public function run()
    {

        if ($this->settings['moderation'] ?? false) {

            $data = $this->createModerationNews();

            echo $this->render('index_moderation', [
                'dataprovider' => $this->dataprovider,
                'settings' => $this->settings,
                'data' => $data,
            ]);
        } else {
            echo $this->render('index', [
                'dataprovider' => $this->dataprovider,
                'settings' => $this->settings,
            ]);
        }


    }

    public static function renderCategories($categories, $city)
    {
        $html = '';
        $tagCategories = [];
        $url_city = $city['url_name'] ? '/' . $city['url_name'] : '';
        if ($categories && is_array($categories)) {
            foreach ($categories as $category) {
                $tagCategories[] = Html::a($category['name'], $url_city . '/' . $category['url_name']);
            }
        }
        $html = implode(', ', $tagCategories);
        return $html;
    }

    public function createModerationNews()
    {

        $data = $this->dataprovider->getModels();

        foreach ($data as &$item) {
            $categoriesQuery = UnderCategory::find();
            $workingHours = [];
            if ($item['main_id'] != null) {
                $categoriesQuery->innerJoin(PostModerationUnderCategory::tableName(),
                    PostModerationUnderCategory::tableName() . '.under_category_id = ' . UnderCategory::tableName() . '.id AND post_id = ' . $item['id']);
                $workingHours = WorkingHoursModeration::find()->where(['post_id' => $item['id']])
                    ->asArray()
                    ->orderBy(['day_type' => SORT_ASC])
                    ->all();

            } else {
                $categoriesQuery->innerJoin(PostUnderCategory::tableName(),
                    PostUnderCategory::tableName() . '.under_category_id = ' . UnderCategory::tableName() . '.id AND post_id = ' . $item['id']);
                $workingHours = WorkingHours::find()->where(['post_id' => $item['id']])
                    ->asArray()
                    ->orderBy(['day_type' => SORT_ASC])
                    ->all();
            }

            $categories = $categoriesQuery->asArray()->all();
            $item['categories'] = $categories;
            $item['city'] = City::find()->where(['id' => $item['city_id']])->asArray()->one();

            $workingHours = ArrayHelper::index($workingHours, 'day_type');
            $currentDay = date('w') == 0 ? 7 : date('w');

            $currentTimestamp = \Yii::$app->formatter->asTimestamp(\Yii::$app->formatter->asTime(time() + \Yii::$app->user->getTimezoneInSeconds(),
                'short'));
            $currentTime = idate('H', $currentTimestamp) * 3600 + idate('i', $currentTimestamp) * 60 + idate('s',
                    $currentTimestamp);

            if (isset($workingHours[$currentDay])) {
                if ($workingHours[$currentDay]['time_start'] < $currentTime && $workingHours[$currentDay]['time_finish'] > $currentTime) {
                    $item['is_open'] = true;
                } else {
                    $item['is_open'] = false;
                }
            } else {
                $item['is_open'] = false;
            }

        }


        return $data;
    }
}