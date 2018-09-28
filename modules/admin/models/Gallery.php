<?php

namespace app\modules\admin\models;

use app\models\entities\Gallery as ParentsGallery;


class Gallery extends ParentsGallery
{


    public $labelStatus = [
        0 => '<span class="moderation">На модерации</span>',
        1 => '<span class="confirm">Проверено</span>',
    ];


    public function getButtons()
    {

        $beginHtml = "<div class='data-grid-container-btn'>";
        $bodyHtml = "";
        $endHtml = "</div>";

        if ($this->status != self::$STATUS['confirm']) {
            $bodyHtml .= "<a title='Одобрить' href='/admin/moderation/act-photo?id={$this->id}&act=confirm' class='btn-moderation --confirm'></a>";
            $bodyHtml .= "<a style='margin-right: 30px' title='Одобрить и дать 2 опыта' href='/admin/moderation/act-photo?id={$this->id}&act=confirm2' class='btn-moderation --confirm'><span class='prop-sb'>2</span></a>";
        }

        $bodyHtml .= "<a title='Удалить' href='/admin/moderation/act-photo?id={$this->id}&act=delete' class='btn-moderation --delete'></a>";

        return $beginHtml . $bodyHtml . $endHtml;

    }

    public function getTextStatus()
    {
        return $this->labelStatus[$this->status];
    }

}
