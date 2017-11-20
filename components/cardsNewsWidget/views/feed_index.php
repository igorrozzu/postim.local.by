<?php
$data = $dataprovider->getModels();
?>

    <?php foreach ($data as $item):?>
        <div class="card-block" data-item-id="<?=$item['id']?>" data-type="news">
            <div class="main-pjax">
                <a href="/<?=$item['url_name'].'-n'.$item['id']?>">
                    <div class="card-photo" style="background-image: url('<?=$item->getPatchCover()?>')">
                        <div class="glass">
                            <div class="bookmarks-btn<?=$item->is_like ? ' active' : ''?>">
                                <?=$item["count_favorites"]?>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="js-href-news">
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
                        <span class="icon icon-comments"></span>
                        <span class="count-t"><?=$item->getTotalComments()?></span>
                    </div>
                    <div class="views">
                        <span class="icon icon-views"></span>
                        <span class="count-t"><?=$item->totalView['count']?></span>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach;?>

<?php if ($hrefNext = $dataprovider->pagination->getLinks()['next']??false): ?>
    <div class="replace-block mg-btm-30" id="<?=$settings['replace-container-id']?>">
        <div class="btn-show-more" data-selector_replace="#<?=$settings['replace-container-id']?>"
             data-href="<?=$hrefNext?>&loadTime=<?=$settings['load-time'] ?? ''?>"><noindex>Показать больше новостей</noindex>
        </div>
    </div>
<?php endif; ?>