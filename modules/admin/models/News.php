<?php

namespace app\modules\admin\models;

use app\models\TotalView;
use Yii;
use app\models\News as ParentNews;
use yii\helpers\FileHelper;


class News extends ParentNews
{

    public function rules()
    {
        return [
            [['city_id'], 'required','message'=>'Выберите город'],
            [['cover'], 'required','message'=>'Укажите фото'],
            [['data','header','description','description_s','key_word_s','title_s'], 'required','message'=>'Введите текст'],
            [['date'], 'required'],
            [['city_id', 'total_view_id', 'count_favorites', 'date'], 'integer'],
            [['header', 'description', 'data',  'key_word_s'], 'string'],
            [['cover'], 'string', 'max' => 100],
            [['id'], 'safe'],
        ];
    }

    public function behaviors()
    {
        return [
            'slug' => [
                'class' => 'app\behaviors\Slug',
                'in_attribute' => 'header',
                'out_attribute' => 'url_name',
            ],
        ];
    }


    public function beforeValidate()
    {

        if(!$this->date){
            $this->date = time();
        }

        if(!$this->count_favorites){
            $this->count_favorites = 0;
        }

        return parent::beforeValidate();

    }

    public function beforeSave($insert)
    {
        if(!$this->total_view_id){
            $totalView = new TotalView(['count'=>0]);
            $totalView->save();
            $this->total_view_id = $totalView->id;

        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $this->savePhoto();
    }


    private function savePhoto(){
        if($this->cover){

            $dir = Yii::getAlias('@webroot/post-img/' . $this->id . '/');
            if (!is_dir($dir)) {
                FileHelper::createDirectory($dir);
            }

            $photoPath = Yii::getAlias('@webroot/post_photo/tmp/' . $this->cover);

            if (file_exists($photoPath)) {
                if(copy($photoPath,$dir.$this->cover)){
                    unlink($photoPath);
                }
            }

        }
    }

    public function getPatchCover(){
        if($this->cover){
            if($this->id){
                $dir = Yii::getAlias('@webroot/post-img/' . $this->id . '/'.$this->cover);
                if(file_exists($dir)){
                    return '/post-img/'. $this->id . '/'.$this->cover;
                }else{
                    $photoPath = Yii::getAlias('@webroot/post_photo/tmp/' . $this->cover);
                    if(file_exists($photoPath)){
                        return '/post_photo/tmp/'.$this->cover;
                    }
                }
            }else{
                $photoPath = Yii::getAlias('@webroot/post_photo/tmp/' . $this->cover);
                if(file_exists($photoPath)){
                    return '/post_photo/tmp/'.$this->cover;
                }
            }
        }

        return '';
    }


}
