<?php
use app\components\breadCrumb\BreadCrumb;
use \app\components\Helper;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;
use \yii\helpers\ArrayHelper;

if(!$post->title){
    $post->title = $post->data.', '.
        mb_strtolower(Yii::t('app/singular',$post->onlyOnceCategories[0]->name)).' в '.
        Yii::t('app/locativus',$post->city->name).', '.
        $post->address.': адрес, телефоны и карта проезда';
}
?>
<div class="margin-top60"></div>
<div id="map_block" class="block-map preload-map">
    <div class="btns-map">
        <div class="action-map" title="Открыть карту"></div>
        <div class="find-me" title="Найти меня"></div>
        <div class="zoom-plus"></div>
        <div class="zoom-minus"></div>
    </div>
    <div id="map" style="display: none"></div>
</div>

<?php
$js = <<<js
    $(document).ready(function() {
      map.setIdPlacesOnMap("$keyForMap");
    });
js;
echo "<script>$js</script>";
?>



<div style="display: flex;justify-content: space-between;">
    <div class="block-content" style="width: 650px">
        <div class="block-content-between cust">
            <h2 class="h2-v">Оригинал</h2>
        </div>
        <div class="block-info-card">
            <?php if($mainPost['address']):?>
                <div class="info-row">
                    <div class="left-block-f1">
                        <div class="address-card"><span>Адрес</span></div>
                        <div class="block-inside">
                            <p class="info-card-text"><?=$mainPost->city['name'].', '.$mainPost['address']?></p>
                            <?php if($mainPost['additional_address']):?>
                                <div class="dop-info"><?=$mainPost['additional_address']?></div>
                            <?php endif;?>
                        </div>
                    </div>
                    <div class="right-block-f">
                        <div class="btn-info-card"></div>
                    </div>
                </div>
            <?php endif;?>
            <?php if($mainPost->metro):?>
                <div class="info-row">
                    <div class="left-block-f1">
                        <div class="metro-card">Метро</div>
                        <div class="block-inside">
                            <p class="info-card-text"><?=$mainPost->metro?></p>
                        </div>
                    </div>
                    <div class="right-block-f">
                        <div class="btn-info-card"></div>
                    </div>
                </div>
            <?php endif;?>
            <?php if($mainPost->info['phones']):?>

                <div class="info-row">
                    <div class="left-block-f1">
                        <div class="phone-card"><span><?=substr($mainPost->info['phones'][0],0,8)?>...</span></div>
                        <div class="block-inside">
                            <p class="info-card-text">
                                Показать телефон
                            </p>
                            <ul class="lists-phones">
                                <?php foreach ($mainPost->info['phones'] as $phone): ?>
                                    <li><?= $phone ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="right-block-f">
                        <div class="btn-info-card"></div>
                    </div>
                </div>
            <?php endif;?>
            <?php if($mainPost->info['web_site']):?>
                <div class="info-row">
                    <div class="left-block-f1">
                        <div class="web-site-card">Веб-сайт</div>
                        <div class="block-inside">
                            <p class="info-card-text">
                                <a target="_blank" rel="nofollow noopener" href="<?=$mainPost->info['web_site']?>"><?=Helper::getDomainNameByUrl($mainPost->info['web_site'])?></a>
                            </p>
                        </div>
                    </div>
                    <div class="right-block-f">
                        <div class="btn-info-card"></div>
                    </div>
                </div>
            <?php endif;?>
            <?php if($mainPost->info['social_networks']):?>
                <div class="info-row">
                    <div class="left-block-f">
                        <div class="title-info-card">Социальные&nbsp;сети</div>
                        <div class="block-inside social-info">
                            <div class="block-social-info">
                                <?php foreach ($mainPost->info['social_networks'] as $key => $social_network):?>
                                    <?php if(is_array($social_network)):?>
                                        <?php foreach ($social_network as $keyItem => $valueItem):?>
                                            <a target="_blank" rel="nofollow noopener" href="<?=$valueItem?>" class="<?=$keyItem?>-icon"></a>
                                        <?php endforeach;?>
                                    <?php else:?>
                                        <a target="_blank" rel="nofollow noopener" href="<?=$social_network?>" class="<?=$key?>-icon"></a>
                                    <?php endif;?>
                                <?php endforeach;?>
                            </div>
                        </div>
                    </div>
                    <div class="right-block-f">
                        <div class="btn-info-card"></div>
                    </div>
                </div>
            <?php endif;?>
            <div class="info-row">
                <div class="left-block-f">
                    <div class="title-info-card">Режим&nbsp;работы</div>
                    <div class="block-inside">
                        <div class="block-time-work">
                            <?php if($mainPost->is_open):?>
                                <div class="open"> Открыто <?=$mainPost->timeOpenOrClosed?></div>
                            <?php else:?>
                                <div class="close"> Закрыто <?=$mainPost->timeOpenOrClosed?></div>
                            <?php endif;?>
                            <?php if($mainPost->is_open || $mainPost->timeOpenOrClosed!==null):?>
                                <div class="block-schedules">
                                    <?php foreach ($mainPost->workingHours as $workingHour):?>
                                        <div class="sh-day">
                                            <div class="sh-title-day"><?=Helper::getShortNameDayById($workingHour['day_type'])?></div>
                                            <div class="sh-time-start"><?=Yii::$app->formatter->asTime($workingHour['time_start'], 'HH:mm')?></div>
                                            <div class="sh-time-finish"><?=Yii::$app->formatter->asTime($workingHour['time_finish'], 'HH:mm')?></div>
                                        </div>
                                    <?php endforeach;?>
                                </div>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
                <div class="right-block-f">
                    <div class="btn-info-card"></div>
                </div>
            </div>
            <?php Helper::getFeature($mainPost->getFeatures())?>
            <?php if($mainPost->requisites):?>
                <div class="info-row">
                    <div class="left-block-f">
                        <div class="title-info-card">Реквизиты</div>
                        <div class="block-inside">
                            <p class="info-card-text"><?=$mainPost->requisites?></p>
                        </div>
                    </div>
                    <div class="right-block-f">
                        <div class="btn-info-card"></div>
                    </div>
                </div>
            <?php endif;?>
            <div class="info-row">
                <div class="left-block-f">
                    <div class="title-info-card">Редакторы</div>
                    <div class="block-inside user-editor">
                        <div class="container-user-editor">
                            <ul>
                                <?php if ($mainPost->info && is_array($mainPost->info->editors_users)): ?>
                                    <?php foreach ($mainPost->info->editors_users as $editor): ?>
                                        <li>
                                            <a href="/id<?= $editor->id ?>">
                                                <img src="<?= $editor->getPhoto() ?>">
                                                <span><?=$editor->name.' '.$editor->surname?></span>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="right-block-f">
                    <div class="btn-info-card"></div>
                </div>
            </div>

            <div class="info-row">
                <div class="left-block-f">
                    <div class="title-info-card">Название</div>
                    <div class="block-inside">
                        <p class="info-card-text"><?=$mainPost->data?></p>
                    </div>
                </div>
                <div class="right-block-f">
                    <div class="btn-info-card"></div>
                </div>
            </div>

            <div class="info-row">
                <div class="left-block-f">
                    <div class="title-info-card">Город</div>
                    <div class="block-inside">
                        <p class="info-card-text"><?=$mainPost->city->name?></p>
                    </div>
                </div>
                <div class="right-block-f">
                    <div class="btn-info-card"></div>
                </div>
            </div>
            <div class="info-row">
                <div class="left-block-f">
                    <div class="title-info-card">Категории</div>
                    <div class="block-inside">
                        <p class="info-card-text"><?=  implode(', ',ArrayHelper::getColumn($mainPost->categories,'name')) ?></p>
                    </div>
                </div>
                <div class="right-block-f">
                    <div class="btn-info-card"></div>
                </div>
            </div>

            <div class="info-row">
                <div class="left-block-f">
                    <div class="title-info-card">Координаты</div>
                    <div class="block-inside">
                        <p class="info-card-text"><?=$mainPost->lat.','.$mainPost->lat?></p>
                    </div>
                </div>
                <div class="right-block-f">
                    <div class="btn-info-card"></div>
                </div>
            </div>



        </div>
        <?php if($mainPost->info && $mainPost->info->article):?>
            <h2 class="h2-c">Описание</h2>
            <div class="block-description-card">
                <?=$mainPost->info->article?>
            </div>
        <?php endif;?>

    </div>


    <div class="block-content" style="width: 650px">
        <div class="block-content-between cust">
            <h2 class="h2-v">Редактированый</h2>
        </div>
        <div class="block-info-card">
            <?php if($post['address']):?>
                <div class="info-row">
                    <div class="left-block-f1">
                        <div class="address-card"><span>Адрес</span></div>
                        <div class="block-inside">
                            <p class="info-card-text"><?=$post->city['name'].', '.$post['address']?></p>
                            <?php if($post['additional_address']):?>
                                <div class="dop-info"><?=$post['additional_address']?></div>
                            <?php endif;?>
                        </div>
                    </div>
                    <div class="right-block-f">
                        <div class="btn-info-card"></div>
                    </div>
                </div>
            <?php endif;?>
            <?php if($post->metro):?>
                <div class="info-row">
                    <div class="left-block-f1">
                        <div class="metro-card">Метро</div>
                        <div class="block-inside">
                            <p class="info-card-text"><?=$post->metro?></p>
                        </div>
                    </div>
                    <div class="right-block-f">
                        <div class="btn-info-card"></div>
                    </div>
                </div>
            <?php endif;?>
            <?php if($post->info['phones']):?>

                <div class="info-row">
                    <div class="left-block-f1">
                        <div class="phone-card"><span><?=substr($post->info['phones'][0],0,8)?>...</span></div>
                        <div class="block-inside">
                            <p class="info-card-text">
                                Показать телефон
                            </p>
                            <ul class="lists-phones">
                                <?php foreach ($post->info['phones'] as $phone): ?>
                                    <li><?= $phone ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="right-block-f">
                        <div class="btn-info-card"></div>
                    </div>
                </div>
            <?php endif;?>
            <?php if($post->info['web_site']):?>
                <div class="info-row">
                    <div class="left-block-f1">
                        <div class="web-site-card">Веб-сайт</div>
                        <div class="block-inside">
                            <p class="info-card-text">
                                <a target="_blank" rel="nofollow noopener" href="<?=$post->info['web_site']?>"><?=Helper::getDomainNameByUrl($post->info['web_site'])?></a>
                            </p>
                        </div>
                    </div>
                    <div class="right-block-f">
                        <div class="btn-info-card"></div>
                    </div>
                </div>
            <?php endif;?>
            <?php if($post->info['social_networks']):?>
                <div class="info-row">
                    <div class="left-block-f">
                        <div class="title-info-card">Социальные&nbsp;сети</div>
                        <div class="block-inside social-info">
                            <div class="block-social-info">
                                <?php foreach ($post->info['social_networks'] as $key => $social_network):?>
                                    <?php if(is_array($social_network)):?>
                                        <?php foreach ($social_network as $keyItem => $valueItem):?>
                                            <a target="_blank" rel="nofollow noopener" href="<?=$valueItem?>" class="<?=$keyItem?>-icon"></a>
                                        <?php endforeach;?>
                                    <?php else:?>
                                        <a target="_blank" rel="nofollow noopener" href="<?=$social_network?>" class="<?=$key?>-icon"></a>
                                    <?php endif;?>
                                <?php endforeach;?>
                            </div>
                        </div>
                    </div>
                    <div class="right-block-f">
                        <div class="btn-info-card"></div>
                    </div>
                </div>
            <?php endif;?>
            <div class="info-row">
                <div class="left-block-f">
                    <div class="title-info-card">Режим&nbsp;работы</div>
                    <div class="block-inside">
                        <div class="block-time-work">
                            <?php if($post->is_open):?>
                                <div class="open"> Открыто <?=$post->timeOpenOrClosed?></div>
                            <?php else:?>
                                <div class="close"> Закрыто <?=$post->timeOpenOrClosed?></div>
                            <?php endif;?>
                            <?php if($post->is_open || $post->timeOpenOrClosed!==null):?>
                                <div class="block-schedules">
                                    <?php foreach ($post->workingHours as $workingHour):?>
                                        <div class="sh-day">
                                            <div class="sh-title-day"><?=Helper::getShortNameDayById($workingHour['day_type'])?></div>
                                            <div class="sh-time-start"><?=Yii::$app->formatter->asTime($workingHour['time_start'], 'HH:mm')?></div>
                                            <div class="sh-time-finish"><?=Yii::$app->formatter->asTime($workingHour['time_finish'], 'HH:mm')?></div>
                                        </div>
                                    <?php endforeach;?>
                                </div>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
                <div class="right-block-f">
                    <div class="btn-info-card"></div>
                </div>
            </div>
            <?php Helper::getFeature($post->getFeatures())?>
            <?php if($post->requisites):?>
                <div class="info-row">
                    <div class="left-block-f">
                        <div class="title-info-card">Реквизиты</div>
                        <div class="block-inside">
                            <p class="info-card-text"><?=$post->requisites?></p>
                        </div>
                    </div>
                    <div class="right-block-f">
                        <div class="btn-info-card"></div>
                    </div>
                </div>
            <?php endif;?>
            <div class="info-row">
                <div class="left-block-f">
                    <div class="title-info-card">Редакторы</div>
                    <div class="block-inside user-editor">
                        <div class="container-user-editor">
                            <ul>
                                <?php if ($post->info && is_array($post->info->editors_users)): ?>
                                    <?php foreach ($post->info->editors_users as $editor): ?>
                                        <li>
                                            <a href="/id<?= $editor->id ?>">
                                                <img src="<?= $editor->getPhoto() ?>">
                                                <span><?=$editor->name.' '.$editor->surname?></span>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="right-block-f">
                    <div class="btn-info-card"></div>
                </div>
            </div>

            <div class="info-row">
                <div class="left-block-f">
                    <div class="title-info-card">Название</div>
                    <div class="block-inside">
                        <p class="info-card-text"><?=$post->data?></p>
                    </div>
                </div>
                <div class="right-block-f">
                    <div class="btn-info-card"></div>
                </div>
            </div>

            <div class="info-row">
                <div class="left-block-f">
                    <div class="title-info-card">Город</div>
                    <div class="block-inside">
                        <p class="info-card-text"><?=$post->city->name?></p>
                    </div>
                </div>
                <div class="right-block-f">
                    <div class="btn-info-card"></div>
                </div>
            </div>
            <div class="info-row">
                <div class="left-block-f">
                    <div class="title-info-card">Категории</div>
                    <div class="block-inside">
                        <p class="info-card-text"><?=  implode(', ',ArrayHelper::getColumn($post->categories,'name')) ?></p>
                    </div>
                </div>
                <div class="right-block-f">
                    <div class="btn-info-card"></div>
                </div>
            </div>

            <div class="info-row">
                <div class="left-block-f">
                    <div class="title-info-card">Координаты</div>
                    <div class="block-inside">
                        <p class="info-card-text"><?=$post->lat.','.$post->lat?></p>
                    </div>
                </div>
                <div class="right-block-f">
                    <div class="btn-info-card"></div>
                </div>
            </div>

        </div>
        <?php if($post->info && $post->info->article):?>
            <h2 class="h2-c">Описание</h2>
            <div class="block-description-card">
                <?=$post->info->article?>
            </div>
        <?php endif;?>

    </div>
</div>



<div class="mg-btm-30"></div>

<script>
    $(document).ready(function() {
        post.info.init();
    })
</script>

