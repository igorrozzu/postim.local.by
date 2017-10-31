<?php

namespace app\modules\admin\models;

use app\models\Comments;
use app\models\entities\Gallery;
use app\models\Reviews;
use app\models\User;
use Yii;
use app\models\entities\Complaints as parentComplaints;
use yii\db\ActiveQuery;

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

                $aLink = "<a data-pjax=false target=\"_blank\" class='data-link' href='{$this->photo->getLink()}'>{$this->data}</a>";
                $this->tagName = $aLink;

            }break;
            case 2:{
                $aLink = "<a data-pjax=false target=\"_blank\" class='data-link' href='{$this->reviews->getLink()}'>{$this->data}</a>";
                $this->tagName = $aLink;
            }break;
            case 3:{

                $aLink = "<a data-pjax=false target=\"_blank\" class='data-link' href='{$this->comments->getLink()}'>{$this->data}</a>";
                $this->tagName = $aLink;

            }break;
        }
    }

    public function getButtons(){

        switch ($this->type) {
            case 1: {
                return "<div>
                        <a href='/admin/moderation/act-complaints?type={$this->type}&act=confirm' class='btn-moderation --confirm'></a>
                        <a href='/admin/moderation/act-complaints?type={$this->type}&act=delete' class='btn-moderation --delete'></a>
                </div>";
            }
                break;
            case 2: {
                return "<div>
                        <a class='btn-moderation --confirm'></a>
                        <a class='btn-moderation --delete'></a>
                        <a class='btn-moderation --cancels'></a>
                </div>";
            }
                break;
            case 3: {
                return "<div>
                        <a class='btn-moderation --confirm'></a>
                        <a class='btn-moderation --delete'></a>
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


}