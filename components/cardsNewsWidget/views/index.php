<?php
$data = $dataprovider->getModels();
?>

<div class="block-news">
    <?php foreach ($data as $item):?>
        <div class="card-block" data-item-id="<?=$item['id']?>" data-type="news">
            <a href="/<?=$item['url_name'].'-n'.$item['id']?>">
                <div class="card-photo" style="background-image: url('<?=$item["cover"]?>')">
                    <div class="glass">
                        <div class="bookmarks-btn<?=$item->is_like ? '-active' : ''?>">
                            <?=$item["count_favorites"]?>
                        </div>
                    </div>
                </div>
            </a>
            <?php
                $tag =['url_name'=>'','name'=>''];

                if (isset($item->city)) {
                    $tag['name'] = $item->city['name'];
                    $tag['url_name'] = $item->city['url_name']?'/' .$item->city['url_name']. '/novosti':'/novosti';
                }
            ?>
            <div class="card-block-info">
                <p class="info-head"><?="<a href='{$tag["url_name"]}'>{$tag["name"]}</a>";?></p>
                <p class="card-info"><?=$item->description?></p>
            </div>
            <div class="block-btn">
                <div class="comments">
                    <span class="icon"><img src="/img/comments-icon.png"></span>
                    <span class="count-t"><?=$item->getTotalComments()?></span>
                </div>
                <div class="views">
                    <span class="icon"><img src="/img/views-icon.png"></span>
                    <span class="count-t"><?=$item->totalView['count']?></span>
                </div>
            </div>
        </div>
    <?php endforeach;?>
</div>

<?php if(count($data)==4):?>
    <div class="clear-fix"></div>
    <div class="main-pjax">
        <a href="<?=Yii::$app->city->getSelected_city()['url_name']?'/'.Yii::$app->city->getSelected_city()['url_name']:''?>/novosti" class="btn-show-more">Показать больше новостей</a>
    </div>
<?php endif;?>