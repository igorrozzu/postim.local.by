<?php

namespace app\modules\admin\models;

use Yii;
use app\models\OtherPage as ParentModel;

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
class OtherPage extends ParentModel
{

    public static $FIND_PAGE = 'find-page';
    public static $EDIT_PAGE = 'edit-page';
    public static $ADD_PAGE = 'add-page';

    public $find_url = '';

    public function rules()
    {
        return [
            [['url_name'], 'required', 'message' => 'Введите адрес страницы', 'on' => self::$EDIT_PAGE],
            [['url_name'], 'required', 'message' => 'Введите адрес страницы', 'on' => self::$FIND_PAGE],
            [['url_name'], 'required', 'message' => 'Введите адрес страницы', 'on' => self::$ADD_PAGE],
            [['url_name', 'description_text'], 'string'],
            [['status'], 'integer'],
            [['h1', 'title', 'description'], 'string', 'max' => 400],
            [['key_word'], 'string', 'max' => 200],
            [['url_name'], 'unique', 'on' => self::$ADD_PAGE],
            [
                ['url_name'],
                'match',
                'pattern' => '/^https?:\/\/postim.*by.*/',
                'message' => 'Введите коректный URL',
                'on' => self::$FIND_PAGE,
            ],
        ];
    }

    public function getData()
    {
        if (!$this->url_name) {
            return false;
        } else {
            return self::find()->where(['url_name' => self::convertUrl($this->url_name)])->one();
        }
    }

    public function beforeValidate()
    {
        if ($this->getScenario() == self::$ADD_PAGE) {
            $this->url_name = self::convertUrl($this->url_name);
        }

        return parent::beforeValidate();
    }

    public function load($data, $formName = null)
    {
        $result = parent::load($data, $formName);

        if ($this->getScenario() == self::$FIND_PAGE) {
            $this->find_url = $this->url_name;
        }

        return $result;
    }

    public function getLabelStatus()
    {
        $label = [
            0 => 'Нет',
            1 => 'Да',
        ];

        if (!$this->status) {
            $this->status = 0;
        }

        return $label[$this->status];
    }

    public function getButtons()
    {

        $beginHtml = "<div class='data-grid-container-btn'>";
        $bodyHtml = "";
        $endHtml = "</div>";

        $bodyHtml .= "<a title='Удалить' href='/admin/post/other-page-delete?url_name={$this->url_name}&act=delete' class='btn-moderation --delete'></a>";

        return $beginHtml . $bodyHtml . $endHtml;
    }

}