<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "tbl_description_page".
 *
 * @property string $url_page
 * @property string $h1
 * @property string $title
 * @property string $description
 * @property string $key_word
 * @property string $description_text
 */
class DescriptionPage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_description_page';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url_page'], 'required'],
            [['url_page', 'description_text'], 'string'],
            [['h1', 'title', 'description'], 'string', 'max' => 400],
            [['key_word'], 'string', 'max' => 200],
            [['url_page'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'url_page' => 'Url Page',
            'h1' => 'H1',
            'title' => 'Title',
            'description' => 'Description',
            'key_word' => 'Key Word',
            'description_text' => 'Description Text',
        ];
    }

    public static function convertUrl(string $url)
    {
        $newUrl = preg_replace('/(https?:\/\/.+?(?=\/))|\?.+/', '', $url);
        return $newUrl;
    }

    public static function initMetaTags($func)
    {

        $response = [
            'title' => '',
            'description' => '',
            'keywords' => '',
            'descriptionText' => '',
            'h1' => '',
        ];

        $response = ArrayHelper::merge($response, call_user_func($func));

        $currentUrl = Url::to('', true);

        $descPage = self::find()->where(['url_page' => self::convertUrl($currentUrl)])->one();
        if ($descPage) {

            if ($descPage['title']) {
                $response['title'] = $descPage['title'];
            }
            if ($descPage['description']) {
                $response['description'] = $descPage['description'];
            }
            if ($descPage['key_word']) {
                $response['keywords'] = $descPage['key_word'];
            }
            if ($descPage['description_text']) {
                $response['descriptionText'] = $descPage['description_text'];
            }
            if ($descPage['h1']) {
                $response['h1'] = $descPage['h1'];
            }
        }

        return $response;


    }
}