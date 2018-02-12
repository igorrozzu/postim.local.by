<?php

use yii\db\Migration;

class m180212_134710_add_field_date_of_execution_for_tbl_tasks extends Migration
{
    public function safeUp()
    {
        $this->execute('alter table tbl_tasks add date_of_execution integer default 0 not null;');
    }

    public function safeDown()
    {
        $this->execute('ALTER TABLE tbl_tasks DROP COLUMN date_of_execution;');
    }
}
