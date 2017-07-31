<?php
$data = $dataprovider->getModels();
?>

    <?php foreach ($data as $item):?>
        <div class="card-block ">
            <a href="/<?=$item['url_name'].'-n'.$item['id']?>">
                <div class="card-photo" style="background-image: url('<?=$item["cover"]?>')">
                    <div class="glass">
                        <div class="bookmarks-btn"><?=$item["count_favorites"]?></div>
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

<?php if ($hrefNext = $dataprovider->pagination->getLinks()['next']??false): ?>
    <div class="replace-block mg-btm-30" id="<?=$settings['replace-container-id']?>">
        <div class="btn-show-more" data-selector_replace="#<?=$settings['replace-container-id']?>" data-href="<?=$hrefNext?>&loadTime=<?=$settings['load-time'] ?? ''?>">Показать больше мест</div>
    </div>
<?php endif; ?>