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
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'js/libs/leaflet/leaflet.css',
        'js/libs/leaflet/markercluster/dist/MarkerCluster.Default.css',
        'js/libs/leaflet/markercluster/dist/MarkerCluster.css',
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
    ];
    public $js = [
        'js/uploads.js',
        'js/main.js',
        'js/menu.js',
        'js/list-city.js',
        'js/libs/jquery.toastmessage.js',
        'js/show-more.js',
        'js/category.js',
        'js/post.js',
        'js/news.js',
		'js/reviews.js',
        'js/comments.js',
        'js/libs/jquery.autosize.js',
        'js/goodShare.js',
        'js/libs/debounce.js',
        'js/libs/jquery-ui.min.js',
        '/js/libs/jquery.touchSwipe.min.js',
        'js/libs/jquery.cookie.js',
        'js/libs/jquery.mask.js',
        'js/libs/medium-editor/dist/js/medium-editor.js',
        'js/editable.js',
        'js/libs/leaflet/leaflet.js',
        'js/libs/leaflet/markercluster/dist/leaflet.markercluster-src.js',
        'js/map.js',
        'js/add-post.js',


    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
