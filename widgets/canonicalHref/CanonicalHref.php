<?php

namespace app\widgets\canonicalHref;

use yii\base\Widget;
use yii\helpers\Url;


class CanonicalHref extends Widget
{

    public function run()
    {
        $currentUrl = Url::to('',true);
        $parseUrl = parse_url($currentUrl);
        $canonicalUrl = false;

        if($parseUrl['query'] ?? false)
        {
            $canonicalUrl = "{$parseUrl['scheme']}://{$parseUrl['host']}{$parseUrl['path']}";
        }

        return $this->render('index', compact('canonicalUrl'));
    }
}