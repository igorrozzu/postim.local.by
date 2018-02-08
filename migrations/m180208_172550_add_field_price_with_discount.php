<?php

use yii\db\Migration;

class m180208_172550_add_field_price_with_discount extends Migration
{
    public function safeUp()
    {
        $this->execute('ALTER TABLE public.tbl_discounts ADD price_with_discount NUMERIC NULL;');
    }

    public function safeDown()
    {
        $this->execute('ALTER TABLE tbl_discounts DROP COLUMN price_with_discount;');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180208_172550_add_field_price_with_discount cannot be reverted.\n";

        return false;
    }
    */
}
