<?php

use yii\db\Migration;

class m180211_112530_add_field_date_finish_for_tbl_discount_order extends Migration
{
    public function safeUp()
    {
        $this->execute('ALTER TABLE public.tbl_discount_order ADD date_finish INT NULL;');
    }

    public function safeDown()
    {
        $this->execute('ALTER TABLE tbl_discount_order DROP COLUMN date_finish;');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180211_112530_add_field_date_finish_for_tbl_discount_order cannot be reverted.\n";

        return false;
    }
    */
}
