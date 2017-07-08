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
        'css/main.css',
        ['css/jquery.mCustomScrollbar.css','media'=>'all'],
        'css/jquery-ui.css',
        ['css/min-1260px.css','media','(min-width: 1320px)'],
        ['css/min-1190px.css','media','(max-width:1319px)'],
        ['css/min-950px.css','media','(max-width:949px)'],
        ['css/min-710px.css','media','(max-width:770px)'],
        ['css/min-540px.css','media','(max-width:540px)'],
        ['css/max-480px.css','media','(max-width:480px)'],
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
