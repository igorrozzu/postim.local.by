<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;
use Yii;
use yii\console\Controller;


/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class CovertController extends Controller
{
    public function actionConvertAddress(){
        $sql = 'UPDATE tbl_posts SET address = regexp_replace(address, \'^Беларусь, \', \'\');';
        Yii::$app->db->createCommand($sql)->execute();
    }
}
