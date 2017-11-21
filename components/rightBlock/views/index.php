<div class="baner">
    <a href="/cto-daet-registracia-na-postmby-n31"><img src="/postim_baner.png"></a>
    <?php if(Yii::$app->user->isGuest):?>
        <div class="btn-standard js-btn-register --btn-red">Регистрация</div>
        <script>
            $(document).ready(function () {
                $('.js-btn-register').off('click').on('click',function () {
                    $( ".sign_in_btn" ).trigger( "click" );
                })
            })
        </script>
    <?php endif;?>
</div>
