<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
$data = $dataProvider->getModels();
$count = count($data);
?>
<?php foreach ($data as $item):?>
    <div class="card-block ">
        <div class="card-photo" style="background-image: url('<?=$item["cover"]?>')">
            <div class="glass">
                <div class="bookmarks-btn"><?=$item["count_favorites"]?></div>
            </div>
        </div>
        <?php
        $tag = isset($item->city->newsCity)?
            $item->city->newsCity: isset($item->city->region->newsRegion)?
                $item->city->region->newsRegion:['url_name'=>'','name'=>''];
        ?>
        <div class="card-block-info">
            <p class="info-head"><?="<a href='{$tag["url_name"]}'>{$tag["name"]}</a>";?></p>
            <p class="card-info"><?=$item->description?></p>
        </div>
        <div class="block-btn">
            <div class="comments">
                <span class="icon"><img src="img/comments-icon.png"></span>
                <span class="count-t"><?=$item->getTotalComments()?></span>
            </div>
            <div class="views">
                <span class="icon"><img src="img/views-icon.png"></span>
                <span class="count-t"><?=$item->totalView['count']?></span>
            </div>
        </div>
    </div>
<?php endforeach;?>

<?php if($count > 0):?>
    <?php if($settings['show-more-btn'] &&
        ($hrefNext = $dataProvider->pagination->getLinks()['next'] ?? false)):?>

        <div class="replace-block mg-btm-30" id="<?=$settings['replace-container-id']?>">
        <div class="btn-show-more" data-selector_replace="#<?=$settings['replace-container-id']?>"
             data-href="<?=$hrefNext?>&loadTime=<?=$settings['load-time']?>">
            Показать больше новостей
        </div>
        </div>
    <?php else:?>
        <div class="replace-block mg-btm-30"></div>
    <?php endif; ?>
<?php else:?>
    <div class="card-promo">
        <p class="card-text-notice">Вы пока не добавили в избранное ни одной новости</p>
    </div>
<?php endif;?>