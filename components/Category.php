<?php
namespace app\components;

use app\models\UnderCategory;
use yii;
use yii\helpers\ArrayHelper;


class Category extends  yii\base\Model {

    private $categories = null;
    private $underCategories = null;

    public function getCategories(){
        if($this->categories==null){
            $this->getIndexCategory();
        }
        return $this->categories;
    }

    public function getCategoryByName(string $name){
        if($this->categories==null){
            $this->getIndexCategory();
        }
        return $this->categories[$name]??false;
    }

    public function getUnderCategories(){
        if($this->underCategories==null){
            $this->getIndexUnderCategory();
        }
        return $this->underCategories;
    }

    public function getUnderCategoryByName(string $name){
        if($this->underCategories==null){
            $this->getIndexUnderCategory();
        }
        return $this->underCategories[$name]??false;
    }



    private function getIndexCategory(){
        if(!$index_category =Yii::$app->cache->get('list_cat_from_bd')){
            $index_category = ArrayHelper::index(\app\models\Category::find()
                ->select(['name','url_name','id'])
                ->orderBy(['name'=>SORT_ASC])
                ->all(),'url_name');
            Yii::$app->cache->add('list_cat_from_bd',$index_category,3600);
        }
        $this->categories=$index_category;
    }
    private function getIndexUnderCategory(){
        if(!$index_under_category =Yii::$app->cache->get('list_under_cat_from_bd')){
            $index_under_category = ArrayHelper::index(UnderCategory::find()
                ->innerJoinWith('category')
                ->orderBy(['tbl_under_category.name'=>SORT_ASC])
                ->all(),'url_name');
            Yii::$app->cache->add('list_under_cat_from_bd',$index_under_category,3600);
        }

        $this->underCategories = $index_under_category;
    }

}