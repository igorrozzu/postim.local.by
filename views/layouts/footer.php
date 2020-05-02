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
                    <div class="block-social">
                        <a href="https://vk.com/postimby"
                           class="social-btn-vk" rel="nofollow" target="_blank"></a>
                        <a href="https://www.facebook.com/postimby"
                           class="social-btn-fb" rel="nofollow" target="_blank"></a>
                        <a href="https://twitter.com/postimby"
                           class="social-btn-tw" rel="nofollow" target="_blank"></a>
                    </div>
                    <div class="block-social">
                        <a href="https://www.ok.ru/postimby"
                           class="social-btn-ok" rel="nofollow" target="_blank"></a>
                        <a href="https://www.instagram.com/postimby"
                           class="social-btn-inst" rel="nofollow" target="_blank"></a>
                        <a href="https://chats.viber.com/postimby/ru"
                           class="social-btn-viber" rel="nofollow" target="_blank"></a>
                    </div>
                </div>
            </div>
            <div class="text-footer">
                ИП Каплан Валерия Аркадьевна, УНП 791174548. Режим работы:  9:00 до 18:00. <!--Эл.&nbsp;почта:&nbsp;<span class="email-address">ask@postim.by.</span>--> ©&nbsp;2016–<?=date('Y')?>&nbsp;Postim.by
            </div>
        </div>
    </div>
</noindex>