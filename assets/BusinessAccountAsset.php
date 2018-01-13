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
class BusinessAccountAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        '/js/business-account.js',
        '/js/libs/datepicker-ru.js'
    ];
    public $css = [
        'css/jquery-ui-datepicker.css'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
