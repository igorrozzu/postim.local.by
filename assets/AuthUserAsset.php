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
class AuthUserAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/admin/pop-up/pop-up.css',
    ];
    public $js = [
        'js/auth-user-menu.js',
        'js/user-settings.js',
		'js/keyword-app.js',
        'js/admin/pop-up.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
