<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAdmAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/reset.css',
        'js/libs/medium-editor/dist/css/medium-editor.css',
        'js/libs/medium-editor/dist/css/themes/default.css',
        'css/main.css',
        'css/jquery.mCustomScrollbar.css',
        'css/jquery-ui.css',
        'css/min-1260px.css',
        'css/min-1190px.css',
        'css/min-950px.css',
        'css/min-710px.css',
        'css/min-540px.css',
        'css/max-480px.css',
        'css/main-media.css',
        'js/libs/jquery-confirm.min.css',
        'css/admin/main.css',
        'css/admin/dataGrid.css',
        'css/admin/detailView.css',
        'css/admin/list-view.css',
        'css/admin/pop-up/pop-up.css',
        'css/jquery-ui-datepicker.css',
    ];
    public $js = [
        'js/uploads.js',
        'js/admin/pop-up.js',
        'js/preview-photo.js',
        'js/libs/Mobile-detect.js',
        'js/libs/jquery.toastmessage.js',
        'js/libs/jquery.autosize.js',
        'js/libs/debounce.js',
        'js/libs/jquery-ui.min.js',
        '/js/libs/jquery.touchSwipe.min.js',
        'js/libs/jquery.cookie.js',
        'js/libs/jquery.mask.js',
        'js/libs/jquery-confirm.min.js',
        'js/main.js',
        'js/admin/main.js',
        'js/menu.js',
        'js/libs/medium-editor/dist/js/medium-editor.js',
        'js/editable.js',
        'js/add-post.js',
        'js/user-settings.js',
        '/js/discount.js',
        '/js/libs/datepicker-ru.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
