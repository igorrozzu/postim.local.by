<?php
use yii\helpers\Url;

?>

<noindex>
    <div class="block-footer">
        <div class="block-footer-content">
            <div class="block-footer-btn">
                <ul class="menu-inline">
                    <li class="main-pjax"><a href="/about" rel="nofollow">О сайте</a></li>
                    <li class="main-pjax"><a href="/review-rules" rel="nofollow">Правила</a></li>
                    <li class="main-pjax"><a href="/agreement" rel="nofollow">Соглашение</a></li>
                    <li>
                        <a href="<?= Url::to(['lading/sale-of-a-business-account'])?>" rel="nofollow">
                            Для бизнеса
                        </a>
                    </li>
                    <li class="main-pjax"><a href="/feedback" rel="nofollow">Обратная связь</a></li>
                </ul>
                <div class="block-social-icons">
                    <div class="block-social" style="width: 80px; height: 46px"></div>
                    <div class="block-social"></div>
                </div>
            </div>
            <div class="text-footer">
                ИП Каплан Валерия Аркадьевна, УНП 791174548. Режим работы:  9:00 до 18:00. <!--Эл.&nbsp;почта:&nbsp;<span class="email-address">ask@postim.by.</span>--> ©&nbsp;2016–<?=date('Y')?>&nbsp;Postim.by
            </div>
        </div>
    </div>
</noindex>