<?php

use app\models\Posts;
use yii\db\Migration;

/**
 * Class m180407_200927_delete_unused_posts
 */
class m180407_200927_delete_unused_posts extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $posts = Posts::find()
            ->joinWith('categories.category')
            ->andWhere(['tbl_under_category.url_name' => 'plyazhi'])
            ->all();

        foreach ($posts as $post) {
            $post->status = Posts::$STATUS['private'];
            $post->save();
        }
        echo count($posts) . " posts from 'plyazhi' category were hide\n";

        $posts = Posts::find()
            ->joinWith(['categories.category', 'photos'])
            ->where(['tbl_under_category.url_name' => 'fontany'])
            ->all();

        $hidedPosts = 0;
        foreach ($posts as $post) {
            if (empty($post->photos)) {
                $post->status = Posts::$STATUS['private'];
                $post->save();
                $hidedPosts++;
            }
        }
        echo $hidedPosts . " from 'fontany' category posts were hide\n";
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180407_200927_delete_unused_posts / posts cannot be repaired.\n";

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180407_200927_delete_unused_posts cannot be reverted.\n";

        return false;
    }
    */
}
