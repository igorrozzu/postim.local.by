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
class CustomScrollbarAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        ['css/jquery.mCustomScrollbar.css','media'=>'all'],
    ];
    public $js = [
        'js/libs/jquery.mCustomScrollbar.concat.min.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
