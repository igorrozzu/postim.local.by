<?php

use yii\db\Migration;

class m180201_234836_create_tbl_recommended_posts extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute('create table if not exists tbl_recommended_posts
        (
            id serial not null
                constraint tbl_recommended_posts_pkey
                    primary key,
            key jsonb not null,
            queue json,
            updating_at integer not null
        );');

        $this->execute('create unique index if not exists tbl_recommended_posts_key_uindex
            on tbl_recommended_posts (key);');
    }

    public function down()
    {
        $this->dropTable('tbl_recommended_posts');
    }
}
