<?php

namespace app\modules\admin\models;

use Yii;
use app\models\UnderCategory as ParentUnderCategory;


class UnderCategory extends ParentUnderCategory
{


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id'], 'required','message'=>'Выберите id категории'],
            [['name','url_name'], 'required','message'=>'Введите текст'],
            [['name'], 'string'],
            [['category_id'], 'integer'],
            [['url_name'], 'unique', 'message'=>'Url должен быть уникальным'],
            [['url_name'], 'string', 'max' => 40],
        ];
    }

    public function getButtons(){

        $beginHtml = "<div class='data-grid-container-btn'>";
        $bodyHtml = "";
        $endHtml = "</div>";

        $bodyHtml.="<a title='Удалить' href='/admin/post/act-delete-categories?id={$this->id}&act=under_category' class='btn-moderation --delete'></a>";

        return $beginHtml.$bodyHtml.$endHtml;
    }

}
