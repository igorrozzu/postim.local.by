<?php

use yii\db\Migration;

class m180127_123350_update_account_payment_columns extends Migration
{
    public function safeUp()
    {
        $this->createTable('tbl_account_payment',[
            'id' => \yii\db\Schema::TYPE_PK,
            'user_id' => \yii\db\Schema::TYPE_INTEGER,
            'money' => \yii\db\Schema::TYPE_DOUBLE,
            'entity' => \yii\db\Schema::TYPE_SMALLINT,
            'status' => \yii\db\Schema::TYPE_INTEGER . ' DEFAULT 0',
            'status_process' => \yii\db\Schema::TYPE_INTEGER . ' DEFAULT 0',
        ]);

        $this->addForeignKey(
            'tbl_account_payment_tbl_users_id_fk',
            'tbl_account_payment',
            'user_id',
            'tbl_users',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->execute("SELECT setval('tbl_account_payment_id_seq', 1000)");


    }

    public function safeDown()
    {

        $this->dropTable('tbl_account_payment');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180127_123350_update_account_payment_columns cannot be reverted.\n";

        return false;
    }
    */
}
