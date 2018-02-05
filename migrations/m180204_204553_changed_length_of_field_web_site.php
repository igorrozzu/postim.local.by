<?php

use yii\db\Migration;

class m180204_204553_changed_length_of_field_web_site extends Migration
{
    public function safeUp()
    {
        $this->execute('ALTER TABLE public.tbl_post_info ALTER COLUMN web_site 
                            TYPE VARCHAR(500) USING web_site::VARCHAR(500);');
    }

    public function safeDown()
    {
        $this->execute('ALTER TABLE public.tbl_post_info ALTER COLUMN web_site 
                            TYPE VARCHAR(50) USING web_site::VARCHAR(50);');
    }
}
