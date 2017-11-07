<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "tbl_other_page".
 *
 * @property string $url_name
 * @property string $h1
 * @property string $title
 * @property string $description
 * @property string $key_word
 * @property string $description_text
 * @property integer $status
 */
class OtherPage extends \yii\db\ActiveRecord
{

    public static $STATUS = [
        'showMenu' => 1,
        'hideMenu' => 0,
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_other_page';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url_name', 'h1', 'description_text'], 'required'],
            [['description_text'], 'string'],
            [['status'], 'integer'],
            [['url_name'], 'string', 'max' => 300],
            [['h1', 'title', 'description', 'key_word'], 'string', 'max' => 500],
            [['url_name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'url_name' => 'Url Name',
            'h1' => 'H1',
            'title' => 'Title',
            'description' => 'Description',
            'key_word' => 'Key Word',
            'description_text' => 'Description Text',
            'status' => 'Status',
        ];
    }


    public static function convertUrl(string $url){
        $newUrl = preg_replace('/(https?:\/\/.+(?=\/))|\?.+/','',$url);
        return $newUrl;
    }

    public static function initMetaTags($func){

        $response = [
            'title' => '',
            'description' => '',
            'keywords' => '',
            'descriptionText' => '',
            'h1' => ''
        ];

        $response = ArrayHelper::merge($response,call_user_func($func));

        $currentUrl = Url::to('',true);

        $descPage = self::find()->where(['url_name'=>self::convertUrl($currentUrl)])->one();
        if($descPage){

            if($descPage['title']){
                $response['title'] = $descPage['title'];
            }
            if($descPage['description']){
                $response['description'] = $descPage['description'];
            }
            if($descPage['key_word']){
                $response['keywords'] = $descPage['key_word'];
            }
            if($descPage['description_text']){
                $response['descriptionText'] = $descPage['description_text'];
            }
            if($descPage['h1']){
                $response['h1'] = $descPage['h1'];
            }
        }

        return $response;



    }
    
    
}