<?php
/**
 * Created by PhpStorm.
 * User: jrborisov
 * Date: 8.7.17
 * Time: 20.30
 */

namespace app\components\ListCityWidget;

use app\models\City;
use app\models\Region;
use yii\base\Widget;
use yii\helpers\ArrayHelper;


class ListCityWidget extends Widget
{
    public $dataprovider = [];
    public $settings = [];

    public function init()
    {
        parent::init();


        if (!$this->dataprovider = \Yii::$app->cache->get('list_city')) {
            $cities = ArrayHelper::toArray(City::find()
                ->select(['name', 'url_name'])
                ->orderBy(['name' => SORT_ASC])
                ->all()
            );
            $dataFirstArrayValues = [
                ['name' => 'Минск', 'url_name' => 'minsk', 'selected-letter' => false],
                ['name' => 'Брест', 'url_name' => 'brest', 'selected-letter' => false],
                ['name' => 'Витебск', 'url_name' => 'vitebsk', 'selected-letter' => false],
                ['name' => 'Гомель', 'url_name' => 'gomel', 'selected-letter' => false],
                ['name' => 'Гродно', 'url_name' => 'grodno', 'selected-letter' => false],
                ['name' => 'Могилев', 'url_name' => 'mogilev', 'selected-letter' => false],
                ['name' => 'Минская область', 'url_name' => 'minskaya-obl', 'selected-letter' => false],
                ['name' => 'Брестская область', 'url_name' => 'brestskaya-obl', 'selected-letter' => false],
                ['name' => 'Витебская область', 'url_name' => 'vitebskaya-obl', 'selected-letter' => false],
                ['name' => 'Гомельская область', 'url_name' => 'gomelskaya-obl', 'selected-letter' => false],
                ['name' => 'Гродненская область', 'url_name' => 'grodnenskaya-obl', 'selected-letter' => false],
                ['name' => 'Могилевская область', 'url_name' => 'mogilevskaya-obl', 'selected-letter' => false],
                ['name' => 'Беларусь', 'url_name' => '', 'selected-letter' => false],
            ];

            $unic_latter = [];
            $dataSecondArrayValues = array_filter($cities, function ($item) use ($dataFirstArrayValues, $unic_latter) {
                $item['selected-letter'] = false;
                if (in_array($item, $dataFirstArrayValues)) {
                    return false;
                }
                return true;
            });


            foreach ($dataSecondArrayValues as &$item) {
                if (!isset($unic_latter[mb_substr($item['name'], 0, 1)], $item['name'][0])) {
                    $unic_latter[mb_substr($item['name'], 0, 1)] = true;
                    $item['selected-letter'] = true;
                } else {
                    $item['selected-letter'] = false;
                }
            }

            $this->dataprovider = ArrayHelper::merge($dataFirstArrayValues, $dataSecondArrayValues);

            \Yii::$app->cache->add('list_city', $this->dataprovider, 600);
        }


    }

    public function run()
    {
        if ($this->settings['is_menu']) {
            echo $this->render('poup-up', ['dataprovider' => $this->dataprovider, 'settings' => $this->settings]);
        } else {
            echo $this->render('index', ['dataprovider' => $this->dataprovider, 'settings' => $this->settings]);
        }

    }
}