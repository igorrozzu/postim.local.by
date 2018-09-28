<?php

namespace app\behaviors;

use yii\db\ActiveRecord;
use yii\helpers\Json;

class SaveJson extends \yii\base\Behavior
{
    public $in_attributes = [];

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'encodeJson',
        ];
    }

    public function encodeJson()
    {

        foreach ($this->in_attributes as $attribute) {
            if (isset($this->owner->{$attribute}) &&
                $this->owner->{$attribute} != null &&
                !$this->isJSON($this->owner->{$attribute})
            ) {
                $this->owner->{$attribute} = Json::encode($this->owner->{$attribute});
            }
        }
    }

    private function isJSON($string)
    {
        return ((is_string($string) && (is_object(json_decode($string)) || is_array(json_decode($string))))) ? true : false;
    }


}