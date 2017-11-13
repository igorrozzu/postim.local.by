<?php
/**
 * Created by PhpStorm.
 * User: jrborisov
 * Date: 13.11.17
 * Time: 15.43
 */

namespace app\behaviors;


use yii\base\Behavior;
use yii\db\ActiveRecord;

class RestrictionActions extends Behavior{

    const EVENT_CHECK_LIMIT = 'checkLimit';

    public $in_attribute = 'id';
    public $type = 'other';
    public $message = 'Вы слишком часто совершаете действия';
    public $limitPerMinute = 10;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_VALIDATE => 'checkLimit'
        ];
    }

    public function checkLimit(){

        $key = [\Yii::$app->user->getId(),$this->type,'action_limit'];

        if($this->owner->isLimit && !$this->owner->getErrors() && !\Yii::$app->user->isModerator()){

            if ($recordsActive = \Yii::$app->cache->get($key)) {

                if ($recordsActive['value'] > $this->limitPerMinute) {

                    if($recordsActive['is_ban']??true){
                        $recordsActive['value'] = $this->limitPerMinute + 4;
                        $recordsActive['timeStart'] = time();
                        $recordsActive['is_ban'] = false;

                        \Yii::$app->cache->delete($key);
                        \Yii::$app->cache->add($key, $recordsActive, 300);
                    }


                    $this->owner->addError($this->in_attribute, $this->message);

                } else {

                    if ((time() - 60) > $recordsActive['timeStart']) {
                        $recordsActive['value'] = 1;
                        $recordsActive['timeStart'] = time();
                    } else {
                        $recordsActive['value']++;
                    }
                    \Yii::$app->cache->delete($key);
                    \Yii::$app->cache->add($key, $recordsActive, 60);
                }

            } else {
                \Yii::$app->cache->delete($key);
                \Yii::$app->cache->add($key, ['value' => 1, 'timeStart' => time()], 60);

            }


        }



    }

}