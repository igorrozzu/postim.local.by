<?php

namespace app\modules\admin\models;

use Yii;
use app\models\CategoryFeatures as ParentModel;

/**
 * This is the model class for table "tbl_category_features".
 *
 * @property integer $category_id
 * @property string $features_id
 *
 * @property Category $category
 * @property Features $features
 */
class CategoryFeatures extends ParentModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_category_features';
    }

    /**
     * @inheritdoc
     */


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'category_id' => 'Category ID',
            'features_id' => 'Features ID',
        ];
    }

    public function getFeaturesList()
    {
        $list = Features::find()
            ->select(['id', 'name'])
            ->where(['main_features' => null])
            ->asArray(true)
            ->orderBy(['name' => SORT_ASC])
            ->all();
        return $list;
    }

    public function getLabelFeatures()
    {

        if (!$this->features_id) {
            return 'Выберите особенность';
        }

        $data = Features::find()
            ->select(['name'])
            ->where(['id' => $this->features_id])
            ->one();

        if ($data) {
            return $data['name'];
        } else {
            return 'Выберите особенность';
        }

    }

    public function getCategoriesList()
    {
        $list = \app\models\Category::find()
            ->select(['id', 'name'])
            ->asArray(true)
            ->orderBy(['name' => SORT_ASC])
            ->all();
        return $list;
    }

    public function getLabelCategories()
    {
        if (!$this->category_id) {
            return 'Выберите категорию';
        }

        $data = \app\models\Category::find()
            ->select(['name'])
            ->where(['id' => $this->category_id])
            ->one();

        if ($data) {
            return $data['name'];
        } else {
            return 'Выберите особенность';
        }
    }


    public function getButtons()
    {

        $beginHtml = "<div class='data-grid-container-btn'>";
        $bodyHtml = "";
        $endHtml = "</div>";

        $bodyHtml .= "<a title='Удалить' href='/admin/features/delete-category-and-features?category_id={$this->category_id}&features_id={$this->features_id}&act=category' class='btn-moderation --delete'></a>";

        return $beginHtml . $bodyHtml . $endHtml;
    }


}
