<?php

namespace app\widgets\cardsRecommendedPlace;

use yii\base\Widget;
use yii\helpers\Html;


class CardsRecommendedPlace extends Widget
{
    public $dataprovider;
    public $settings;

    public function run()
    {
        return $this->render('index', [
            'dataprovider' => $this->dataprovider,
            'settings' => $this->settings,
        ]);
    }

    public static function renderTextCategories($categories)
    {
        $tagCategories = [];

        if ($categories && is_array($categories)) {
            foreach ($categories as $category) {
                $tagCategories[] = $category['name'];
            }
        }

        return implode(', ', $tagCategories);
    }
}