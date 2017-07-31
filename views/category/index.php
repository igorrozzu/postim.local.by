<?php
use \app\components\mainMenu\MainMenuWidget;
use \app\components\cardsPlaceWidget\CardsPlaceWidget;
use \app\components\cardsNewsWidget\CardsNewsWidget;
use \app\components\ListCityWidget\ListCityWidget;
use \yii\widgets\Pjax;
use \app\components\breadCrumb\BreadCrumb;
?>
<div class="margin-top60"></div>
<div id="map_block" class="block-map"></div>

<div class="block-content">
    <?= BreadCrumb::widget(['breadcrumbParams'=>$breadcrumbParams])?>
    <?php
        $categoryText = $this->context->under_category?$this->context->under_category['name']:$this->context->category['name'];
        $h1_text=$categoryText.' в '.Yii::t('app/locativus',Yii::$app->city->getSelected_city()['name']);
        $this->title = $h1_text;
    ?>
    <h1 class="h1-v"><?=$h1_text?></h1>
</div>
<?php
Pjax::begin([
    'timeout' => 60000,
    'enablePushState' => false,
    'id' => 'feed-category',
    'linkSelector' => '#feed-category .block-sort a',
    'formSelector' => false,
])
?>
<div class="block-flex-white">
    <div class="block-content">
        <div class="block-sort">
            <a href="<?=$url?>" class="btn-sort <?=$sort=='rating'?'active':''?>">По рейтингу</a>
            <a href="<?=$url.'?sort=new'?>" class="btn-sort <?=$sort=='new'?'active':''?>">Новые</a>
            <a href="<?=$url.'?sort=nigh'?>" class="btn-sort <?=$sort=='nigh'?'active':''?>">Рядом</a>
        </div>
    </div>
</div>
<div class="block-content">
    <?php if($dataProvider->totalCount):?>
        <div class="cards-block">
            <?= CardsPlaceWidget::widget([
                'dataprovider' => $dataProvider,
                'settings'=>[
                    'show-more-btn'=>true,
                    'replace-container-id' => 'feed-posts',
                    'load-time' => $loadTime,
                ]
            ]); ?>
        </div>
    <?php else:?>
        <div class="container-message">
            <p>К сожалению, в <?=Yii::t('app/locativus',Yii::$app->city->getSelected_city()['name'])?> на сайте <?=ucfirst(Yii::$app->getRequest()->serverName)?> в категории <?=$categoryText?> пока нет ничего. Если вы знаете о подходящем месте, добавьте его через кнопку <a href="#">"Добавить место"</a>. Это бесплатно, да еще и выгодно. За размещение новых мест на сайте мы начисляем <a href="#">бонусы</a> пользователям.</p>
        </div>
    <?php endif;?>

</div>
<?php
$settings= [
        'select_category'=>$this->context->category ?? false,
        'select_under_category'=>$this->context->under_category ?? false
];

if ($settings['select_category']) {
    $select_category = $settings["select_category"]['name'];
    $select_under_category = $settings["select_under_category"]['name'] ?? 'NuN';
$js = <<<js
        $(document).ready(function() {
          menu.openCategoryInLeftMenu('$select_category','$select_under_category')
        });
js;
}
echo "<script>$js</script>";

Pjax::end();

?>
