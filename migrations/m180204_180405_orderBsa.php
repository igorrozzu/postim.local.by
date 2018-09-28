<?php

use yii\db\Migration;

class m180204_180405_orderBsa extends Migration
{
    public function safeUp()
    {
        $this->execute('CREATE TABLE tbl_bid_business_order
(
    id SERIAL PRIMARY KEY NOT NULL,
    status INT DEFAULT 0,
    type INT,
    position VARCHAR(100),
    date int,
    email VARCHAR(100),
    full_name VARCHAR(50),
    company_name VARCHAR(50),
    phone VARCHAR(30)
)');
        $this->execute('CREATE UNIQUE INDEX tbl_bid_business_order_id_uindex ON tbl_bid_business_order (id)');
    }

    public function safeDown()
    {
        $this->dropTable('tbl_bid_business_order');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180204_180405_orderBsa cannot be reverted.\n";

        return false;
    }
    */
}
