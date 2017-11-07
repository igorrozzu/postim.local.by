<?php

namespace app\modules\admin\models;

use app\models\Comments;
use app\models\entities\Gallery;
use app\models\Reviews;
use app\models\User;
use Yii;
use app\models\entities\Complaints as parentComplaints;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tbl_complaints".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $data
 * @property string $date
 * @property integer $type
 * @property integer $status
 */
class Complaints extends parentComplaints
{
    private $tagName;


    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->creatingInfoName();

    }

    public function getInfoForName(){
        return $this->tagName;
    }

    private function creatingInfoName(){
        switch ($this->type){
            case 1:{

                if($this->photo){
                    $aLink = "<a data-pjax=false target=\"_blank\" class='data-link' href='{$this->photo->getLink()}'>{$this->data} (Фото)</a>";
                    $this->tagName = $aLink;
                }else{
                    $this->tagName = $this->data.' (удалено)';
                }



            }break;
            case 2: {
                if ($this->reviews) {
                    $aLink = "<a data-pjax=false target=\"_blank\" class='data-link' href='{$this->reviews->getLink()}'>{$this->data} (Отзыв)</a>";
                    $this->tagName = $aLink;
                }else{
                    $this->tagName = $this->data.' (удалено)';
                }

            }break;
            case 3:{
                if($this->comments){
                    if($link =$this->comments->getLink()){
                        $aLink = "<a data-pjax=false target=\"_blank\" class='data-link' href='{}'>{$this->data} (Комментарий)</a>";
                        $this->tagName = $aLink;
                    }else{
                        $this->tagName = $this->data.' (удалено)';
                    }

                }else{
                    $this->tagName = $this->data.' (удалено)';
                }


            }break;
        }
    }

    public function getButtons(){

        switch ($this->type) {
            case 1: {
                return "<div class='data-grid-container-btn'>
                        <a title='Одобрить' href='/admin/moderation/act-complaints?type={$this->type}&user_id={$this->user_id}&entities_id={$this->entities_id}&act=confirm' class='btn-moderation --confirm'></a>
                        <a title='Удалить' href='/admin/moderation/act-complaints?type={$this->type}&user_id={$this->user_id}&entities_id={$this->entities_id}&act=delete' class='btn-moderation --delete'></a>
                </div>";
            }
                break;
            case 2: {
                return "<div class='data-grid-container-btn'>
                        <a title='Одобрить' href='/admin/moderation/act-complaints?type={$this->type}&user_id={$this->user_id}&entities_id={$this->entities_id}&act=confirm' class='btn-moderation --confirm'></a>
                        <a title='Удалить' href='/admin/moderation/act-complaints?type={$this->type}&user_id={$this->user_id}&entities_id={$this->entities_id}&act=delete' class='btn-moderation --delete'></a>
                        <span data-id='{$this->entities_id}' title='Скрыть' class='btn-moderation --cancels'></span>
                </div>";
            }
                break;
            case 3: {
                return "<div class='data-grid-container-btn'>
                        <a title='Одобрить' href='/admin/moderation/act-complaints?type={$this->type}&user_id={$this->user_id}&entities_id={$this->entities_id}&act=confirm' class='btn-moderation --confirm'></a>
                        <a title='Удалить' href='/admin/moderation/act-complaints?type={$this->type}&user_id={$this->user_id}&entities_id={$this->entities_id}&act=delete' class='btn-moderation --delete'></a>
                </div>";
            }
                break;
        }

    }

    public function getPhoto(){
        return $this->hasOne(Gallery::className(), ['id' => 'entities_id']);
    }

    public function getReviews(){
        return $this->hasOne(Reviews::className(), ['id' => 'entities_id']);
    }

    public function getComments(){
        return $this->hasOne(Comments::className(), ['id' => 'entities_id']);
    }

    public static function getModelByType($type):string {

        switch ($type){
            case 1 : {
                return Gallery::className();
            }break;

            case 2 : {
                return Reviews::className();
            }break;

            case 3 : {
                return Comments::className();
            }break;
        }

    }

    public function getStatus(){
        $labelStatus = [
            1 => '<span class="moderation">На модерации</span>',
            2 => '<span class="confirm">Проверено</span>'
        ];

        return $labelStatus[$this->status];

    }


}