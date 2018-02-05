<?php

use yii\db\Migration;

class m180204_210942_changed_url_for_post_with_id_19059 extends Migration
{
    public function safeUp()
    {
        $this->execute('update tbl_posts set url_name=\'ajdi-bar\' where id=19059;');
    }

    public function safeDown()
    {
        $this->execute('update tbl_posts set url_name=\'id-bar\' where id=19059;');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180204_210942_changed_url_for_post_with_id_19059 cannot be reverted.\n";

        return false;
    }
    */
}
