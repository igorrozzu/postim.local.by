<?php

namespace app\modules\admin\models;

use phpDocumentor\Reflection\Types\Self_;
use Yii;
use \app\models\DescriptionPage as ParentDescriptionPage;


class DescriptionPage extends ParentDescriptionPage
{

    public static $FIND_PAGE = 'find-page';
    public static $EDIT_PAGE = 'edit-page';
    public static $ADD_PAGE = 'add-page';

    public $find_url = '';

    public function rules()
    {
        return [
            [['url_page'], 'required', 'message' => 'Введите адрес страницы', 'on' => self::$EDIT_PAGE],
            [['url_page'], 'required', 'message' => 'Введите адрес страницы', 'on' => self::$FIND_PAGE],
            [['url_page'], 'required', 'message' => 'Введите адрес страницы', 'on' => self::$ADD_PAGE],
            [['url_page', 'description_text'], 'string'],
            [['h1', 'title', 'description'], 'string', 'max' => 400],
            [['key_word'], 'string', 'max' => 200],
            [['url_page'], 'unique', 'on' => self::$ADD_PAGE],
            [
                ['url_page'],
                'match',
                'pattern' => '/^https?:\/\/postim.*by.*/',
                'message' => 'Введите коректный URL',
                'on' => self::$FIND_PAGE,
            ],
        ];
    }


    public function getData()
    {
        if (!$this->url_page) {
            return false;
        } else {
            return self::find()->where(['url_page' => self::convertUrl($this->url_page)])->one();
        }
    }

    public function beforeValidate()
    {
        if ($this->getScenario() == self::$ADD_PAGE) {
            $this->url_page = self::convertUrl($this->url_page);
        }

        return parent::beforeValidate();
    }

    public function load($data, $formName = null)
    {
        $result = parent::load($data, $formName);

        if ($this->getScenario() == self::$FIND_PAGE) {
            $this->find_url = $this->url_page;
        }

        return $result;
    }
}