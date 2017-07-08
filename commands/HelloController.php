<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($search)
    {
        $arr = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50];
        echo "найденое число на  ".$this->binarySearch($search,$arr). " позиции\n\r";

    }

    private function binarySearch($search_value,$arr){
        $position_left = 0;
        $position_right = count($arr) - 1;
        $count=0;
        while ($position_left <= $position_right){
            $count++;
            $mid = round(($position_left+$position_right) / 2);
            if($arr[$mid] == $search_value){
                echo $count . " итерации занял поиск \r\n";
                return $mid;
            }
            if($arr[$mid] > $search_value){
                $position_right = $mid -1;
            }else{
                $position_left = $mid +1;
            }
        }
        return null;

    }
}
