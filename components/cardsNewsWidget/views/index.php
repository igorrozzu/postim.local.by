<div class="block-news">
    <?php foreach ($dataprovider as $item):?>
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
</div>

<?php if(count($dataprovider)==4):?>
    <div class="clear-fix"></div>
    <div class="btn-show-more">Показать больше новостей</div>
<?php endif;?>