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
        'css/reset.css',
        'js/libs/leaflet/leaflet.css',
        'js/libs/leaflet/markercluster/dist/MarkerCluster.Default.css',
        'js/libs/leaflet/markercluster/dist/MarkerCluster.css',
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
        'css/admin/pop-up/pop-up.css',
    ];
    public $js = [
        'js/uploads.js',
        'js/libs/Mobile-detect.js',
        'js/libs/lazy-load-img.js',
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
        'js/search.js',
        'js/libs/jquery.ui.touch-punch.min.js',
        'js/modalWindow.js',
        'js/discount.js',
        'js/admin/pop-up.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
