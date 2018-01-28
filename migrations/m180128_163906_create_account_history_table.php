<?php

use yii\db\Migration;

/**
 * Handles the creation of table `account_history`.
 */
class m180128_163906_create_account_history_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->execute('create table if not exists tbl_account_history
        (
            id serial not null
                constraint tbl_account_history_pkey
                    primary key,
            user_id integer not null
                constraint tbl_account_history_tbl_users_id_fk
                    references tbl_users
                        on update cascade on delete cascade,
            changing numeric not null,
            message text not null,
            type smallint not null,
            date integer not null
        );');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('tbl_account_history');
    }
}
