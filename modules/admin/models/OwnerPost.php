<?php

namespace app\modules\admin\models;

use app\models\Posts;
use app\models\User;
use app\modules\admin\models\BusinessOrder;
use app\models\entities\OwnerPost as ParentOwnerPost;
use Yii;

/**
 * This is the model class for table "tbl_owner_post".
 *
 * @property integer $owner_id
 * @property integer $post_id
 *
 * @property Posts $post
 * @property User $owner
 */
class OwnerPost extends ParentOwnerPost
{

    public function rules()
    {
        return [
            [['owner_id', 'post_id'], 'required', 'message' => 'Введите значение'],
            [['owner_id', 'post_id'], 'integer', 'message' => 'Введите корректное значение'],
            [
                ['owner_id'],
                'unique',
                'targetAttribute' => ['owner_id', 'post_id'],
                'message' => 'Данный пользователь уже есть в таблице',
            ],
            [
                ['post_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Posts::className(),
                'targetAttribute' => ['post_id' => 'id'],
                'message' => 'Места с данным id не существует',
            ],
            [
                ['owner_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => ['owner_id' => 'id'],
                'message' => 'Пользователя с данным id не существует',
            ],
        ];
    }

}
