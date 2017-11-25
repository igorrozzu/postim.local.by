<?php

namespace app\modules\admin\models;

use Yii;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "tbl_right_baner".
 *
 * @property integer $id
 * @property string $href
 * @property string $src
 */
class RightBaner extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_right_baner';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['href', 'src'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'href' => 'Href',
            'src' => 'Src',
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $this->savePhoto();
    }


    private function savePhoto(){
        if($this->src){

            $dir = Yii::getAlias('@webroot/baners/');
            if (!is_dir($dir)) {
                FileHelper::createDirectory($dir);
            }

            $photoPath = Yii::getAlias('@webroot/post_photo/tmp/' . $this->src);

            if (file_exists($photoPath)) {
                if(copy($photoPath,$dir.$this->src)){
                    unlink($photoPath);
                }
            }

        }
    }

    public function getPatchCover(){
        if($this->src){
            $dir = Yii::getAlias('@webroot/baners/' . $this->src);
            if(file_exists($dir)){
                return '/baners/'. $this->src;
            }else{
                $photoPath = Yii::getAlias('@webroot/post_photo/tmp/' . $this->src);
                if(file_exists($photoPath)){
                    return '/post_photo/tmp/'.$this->src;
                }
            }
        }

        return '';
    }



}