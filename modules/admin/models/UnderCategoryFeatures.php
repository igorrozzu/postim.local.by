<?php

namespace app\modules\admin\models;

use Yii;
use app\models\UnderCategoryFeatures as ParentModel;


class UnderCategoryFeatures extends ParentModel
{


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
        $list = \app\models\UnderCategory::find()
            ->select(['id', 'name'])
            ->asArray(true)
            ->orderBy(['name' => SORT_ASC])
            ->all();
        return $list;
    }

    public function getLabelCategories()
    {
        if (!$this->under_category_id) {
            return 'Выберите категорию';
        }

        $data = \app\models\UnderCategory::find()
            ->select(['name'])
            ->where(['id' => $this->under_category_id])
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

        $bodyHtml .= "<a title='Удалить' href='/admin/features/delete-category-and-features?under_category_id={$this->under_category_id}&features_id={$this->features_id}&act=under_category' class='btn-moderation --delete'></a>";

        return $beginHtml . $bodyHtml . $endHtml;
    }


}
