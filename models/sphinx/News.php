<?php
namespace  app\models\sphinx;
use app\models\News as NewsEntity;
use yii\sphinx\ActiveRecord;

class News extends ActiveRecord
{
    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function indexName()
    {
        return 'news_search';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return $this->hasOne(NewsEntity::className(), ['id' => 'id']);
    }
}