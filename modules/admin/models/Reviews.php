<?php

namespace app\modules\admin\models;

use app\models\Reviews as ParentReviews;
use yii\base\Model;


class Reviews extends ParentReviews
{

    public function rules()
    {
        return [
            [['status'], 'required'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function getButtons(){

        $beginHtml = "<div class='data-grid-container-btn'>";
        $bodyHtml = "";
        $endHtml = "</div>";

        if($this->status != self::$STATUS['confirm']){
            $bodyHtml.="<a title='Одобрить' href='/admin/moderation/act-reviews?id={$this->id}&act=confirm' class='btn-moderation --confirm'></a>";
            $bodyHtml.="<a style='margin-right: 30px' title='Одобрить и дать 10 опыта' href='/admin/moderation/act-reviews?id={$this->id}&act=confirm10' class='btn-moderation --confirm'><span class='prop-sb'>10</span></a>";
            $bodyHtml.="<a style='margin-right: 30px' title='Одобрить и дать 12 опыта' href='/admin/moderation/act-reviews?id={$this->id}&act=confirm12' class='btn-moderation --confirm'><span class='prop-sb'>12</span></a>";
            $bodyHtml.="<a style='margin-right: 30px' title='Одобрить и дать 15 опыта' href='/admin/moderation/act-reviews?id={$this->id}&act=confirm15' class='btn-moderation --confirm'><span class='prop-sb'>15</span></a>";

        }

        $bodyHtml.="<a title='Удалить' href='/admin/moderation/act-reviews?id={$this->id}&act=delete' class='btn-moderation --delete'></a>";

        if($this->status != self::$STATUS['private']){
            $bodyHtml.="<span data-id='{$this->id}' title='Скрыть' class='btn-moderation --cancels'></span>";
        }

        return $beginHtml.$bodyHtml.$endHtml;
    }

    public $labelStatus = [
        0 => '<span class="moderation">На модерации</span>',
        1 => '<span class="cancels">Скрыто</span>',
        2 => '<span class="confirm">Проверено</span>',
    ];

    public function getTextStatus(){
        return $this->labelStatus[$this->status];
    }

}
