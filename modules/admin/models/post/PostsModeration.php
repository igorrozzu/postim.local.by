<?php

namespace app\modules\admin\models\post;

use app\models\moderation_post\PostsModeration as ParentsModel;
use app\models\PostFeatures;
use app\models\PostInfo;
use app\models\Posts;
use app\models\PostUnderCategory;
use app\models\WorkingHours;
use yii\helpers\Html;


class PostsModeration extends ParentsModel
{


    public function replacement($id){

        $mainPost = Posts::find()->with([
            'info','postCategory','postFeatures',
            'workingHours' => function ($query) {
                $query->orderBy(['day_type' => SORT_ASC]);
            },
        ])
            ->where(['id' => $id])
            ->one();

        $transaction = \Yii::$app->db->beginTransaction();
        $error = false;

        if($mainPost->info){
            $mainPost->info->delete();
        }

        $mainPost->city_id = $this->city_id;
        $mainPost->cover = $this->cover;
        $mainPost->data = Html::encode(Html::decode($this->data));
        $mainPost->status = self::$STATUS['confirm'];
        $mainPost->address = Html::encode(Html::decode($this->address));
        $mainPost->additional_address = Html::encode(Html::decode($this->additional_address));
        $mainPost->date = time();
        $mainPost->coordinates = $this->coordinates;
        $mainPost->metro = $this->metro;
        $mainPost->requisites = Html::encode(Html::decode($this->requisites));

        $lol = $mainPost->validate();

        if($mainPost->update()){

            $postInfo = new PostInfo();
            $postInfo->phones = $this->info->phones;
            $postInfo->web_site = $this->info->web_site;
            $postInfo->social_networks = $this->info->social_networks;
            $postInfo->editors = $this->info->editors;
            $postInfo->article = $this->info->article;
            $postInfo->post_id = $id;

            if(!$postInfo->save()){
                $error = true;
            }

            foreach ($mainPost->postCategory as $item){
                $item->delete();
            }

            foreach ($this->postCategory as $item){
                $post_under_category = new PostUnderCategory(['post_id' => $id,
                        'under_category_id' => $item->under_category_id,
                        'priority' => $item->priority
                    ]
                );

                if(!$post_under_category->save()){
                    $error = true;
                }
            }


            PostFeatures::deleteAll(['post_id'=>$id]);

            if($this->postFeatures){
                foreach ($this->postFeatures as $feature){
                    $postFeatures = new PostFeatures([
                        'post_id' => $feature->post_id,
                        'features_id' => $feature->features_id,
                        'value' => $feature->value,
                        'features_main_id' => $feature->features_main_id,
                    ]);
                    if(!$postFeatures->save()){
                        $error = true;
                    }
                }
            }

            WorkingHours::deleteAll(['post_id'=>$id]);

            if($this->workingHours){
                foreach ($this->workingHours as $workingHour){
                    $workingHours = new WorkingHours([
                        'day_type'=>$workingHour->day_type,
                        'time_start'=>$workingHour->time_start,
                        'time_finish'=>$workingHour->time_finish,
                        'post_id'=>$id,
                    ]);
                    if(!$workingHours->save()){
                        $error = true;
                    }
                }
            }

            if($error){
                $transaction->rollBack();
            }else{
                $transaction->commit();
            }


        }

        return !$error;
    }

}
