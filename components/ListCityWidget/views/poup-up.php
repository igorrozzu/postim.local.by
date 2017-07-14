<?php
use yii;
?>
<div class="container-selected-city">
    <div class="container-header-selected-city">
        <div class="header-selected-city">
            <div class="selected-city"><?=\Yii::$app->city->Selected_city['name']?></div>
            <div class="close-selected-city"></div>
        </div>
    </div>
    <div class="container-body-selected-city">
        <div class="container-cities"  id="<?=$settings['id']?>">
            <div class="block-cities">
                <div class="search-cities">
                    <span class="btn-search2"></span>
                    <input class="search-cities-i" type="text" placeholder="Поиск по названию, выберите ваш город">
                </div>
                <div class="autocomplete-result-search">
                    <ul class="block-list-cities">
                        <?php
                        foreach ($dataprovider as $item){
                            if($item['selected-letter']){
                                echo "<li data-url_name='{$item['url_name']}'><b class='selected-letter'>".mb_substr($item['name'],0,1)."</b>".mb_substr($item['name'],1,strlen($item['name']))."</li>";
                            }else{
                                echo " <li data-url_name='{$item['url_name']}'>{$item['name']}</li>";
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php
    $selector='#'.$settings["id"];
    $dataprovider = \yii\helpers\Json::encode($dataprovider);
    $js = <<<JS
    $(document).ready(function() {
      var params = {
        selector:'{$selector}',
        dataCities:JSON.parse('{$dataprovider}')
      }

      var lautocomplete = listCity.Autocomplete(params);
      lautocomplete.init();
    })
    

JS;
    echo "<script>$js</script>";
    ?>
</div>
