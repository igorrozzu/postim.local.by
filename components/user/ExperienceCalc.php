<?php

namespace app\components\user;

use Yii;

class ExperienceCalc
{
    /**
     * Formula: d*x^2 + (2*a1 - d)*x - 2*exp = 0
     *
     * @param int $expPoints
     * @return int
     *
     */
    public static function getLevelByExperience(int $expPoints): int
    {
        if ($expPoints === 0) {
            return 0;
        }

        $increment = Yii::$app->params['user.incrementExperience'];
        $a = $increment;
        $b = 2 * Yii::$app->params['user.experienceForFirstLevel'] - $increment;
        $c = -2 * $expPoints;

        $d = (pow($b, 2)) - (4 * $a * $c);

        if ($d <= 0) {
            $d = (-1) * $d;
        }

        $x1 = (-2 * $c) / ($b + (sqrt($d)));
        $x2 = (-2 * $c) / ($b - (sqrt($d)));
        $maxRoot = $x1 > $x2 ? $x1 : $x2;

        return (int)abs($maxRoot);
    }

    /**
     * Formula: d*x^2 + (2*a1 - d)*x - 2*exp = 0
     * Experience: (d*x + (2*a1 - d)) * x / 2
     *
     * @param int $level
     * @return int
     */
    public static function getMinExperienceByLevel(int $level): int
    {
        if ($level === 0) {
            return 0;
        }

        $increment = Yii::$app->params['user.incrementExperience'];
        return (int)($increment * $level + (2 * Yii::$app->params['user.experienceForFirstLevel'] -
                    $increment)) * $level / 2;
    }

    /**
     * @param int $level
     * @param int $expPoints
     * @return \stdClass
     */
    public static function getExperienceInfo(int $level, int $expPoints): \stdClass
    {
        $expForCurrentLevel = self::getMinExperienceByLevel($level);
        $expForNextLevel = self::getMinExperienceByLevel($level + 1);

        $result = new \stdClass();
        $result->persent = (int)(($expPoints - $expForCurrentLevel) /
            ($expForNextLevel - $expForCurrentLevel) * 100);
        $result->needExpForNextLevel = $expForNextLevel - $expPoints;

        return $result;
    }
}