<?php

namespace app\models\sphinx;

use app\models\Posts;
use yii\sphinx\ActiveRecord;

class Post extends ActiveRecord
{
    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function indexName()
    {
        return 'post_search';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(Posts::className(), ['id' => 'id']);
    }
}