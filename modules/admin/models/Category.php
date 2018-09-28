<?php

namespace app\modules\admin\models;

use Yii;
use app\models\Category as ParentsCategory;


class Category extends ParentsCategory
{


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'url_name'], 'required', 'message' => 'Введите текст'],
            [['name'], 'string'],
            [['url_name'], 'unique', 'message' => 'Url должен быть уникальным'],
            [['url_name'], 'string', 'max' => 40],
        ];
    }

    public function getButtons()
    {

        $beginHtml = "<div class='data-grid-container-btn'>";
        $bodyHtml = "";
        $endHtml = "</div>";

        $bodyHtml .= "<a title='Удалить' href='/admin/post/act-delete-categories?id={$this->id}&act=category' class='btn-moderation --delete'></a>";

        return $beginHtml . $bodyHtml . $endHtml;
    }


}
