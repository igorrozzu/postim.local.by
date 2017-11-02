<?php

namespace app\modules\admin\models;

use app\models\entities\Gallery as ParentsGallery;


class Gallery extends ParentsGallery
{

    public static $STATUS = [
        'moderation' => 0,
        'confirm' => 1,
    ];

    public $labelStatus = [
        0 => '<span class="moderation">На модерации</span>',
        1 => '<span class="confirm">Проверено</span>'
    ];


    public function getButtons(){
        return "<div class='data-grid-container-btn'>
                        <a title='Одобрить' href='/admin/moderation/act-photo?id={$this->id}&act=confirm' class='btn-moderation --confirm'></a>
                        <a title='Удалить' href='/admin/moderation/act-photo?id={$this->id}&act=delete' class='btn-moderation --delete'></a>
                </div>";
    }

    public function getTextStatus(){
        return $this->labelStatus[$this->status];
    }

}
