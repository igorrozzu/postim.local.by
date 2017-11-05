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
		'js/libs/medium-editor/dist/css/medium-editor.css',
		'js/libs/medium-editor/dist/css/themes/default.css',
        'css/main.css',
        ['css/jquery.mCustomScrollbar.css','media'=>'all'],
        'css/jquery-ui.css',
        ['css/min-1260px.css','media'=>'(min-width: 1320px)'],
        ['css/min-1190px.css','media'=>'(max-width:1319px)'],
        ['css/min-950px.css','media'=>'(max-width:949px)'],
        ['css/min-710px.css','media'=>'(max-width:770px)'],
        ['css/min-540px.css','media'=>'(max-width:540px)'],
        ['css/max-480px.css','media'=>'(max-width:480px)'],
        'css/main-media.css',
        'css/admin/main.css',
        'css/admin/dataGrid.css',
        'css/admin/detailView.css',
        'css/admin/list-view.css',
        'css/admin/pop-up/pop-up.css',
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
        'js/main.js',
        'js/admin/main.js',
        'js/menu.js',
        'js/libs/medium-editor/dist/js/medium-editor.js',
        'js/editable.js',
        'js/add-post.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
