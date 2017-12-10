<?php

namespace app\behaviors;
use yii\db\ActiveRecord;

class Purifier extends \yii\base\Behavior{
    public $in_attribute = 'article';

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'process'
        ];
    }

    public function process(){

        if($this->owner->{$this->in_attribute}){

            $config = \HTMLPurifier_Config::createDefault();
            $config->set('HTML.AllowedElements', ['div','p','b','i','br','a','blockquote','h2','iframe','img']);
            $config->set('HTML.AllowedAttributes', ['div.class','img.src','a.href', 'a.target', 'a.rel','iframe.src','iframe.allowfullscreen']);
            $config->set('Attr.AllowedClasses', ['insert-item','video','block-photo-post','photo-desc','']);
            $config->set('Attr.AllowedRel', ['nofollow','noopener']);
            $config->set('Attr.AllowedFrameTargets', ['_blank']);

            $config->set('HTML.SafeIframe', true);
            $config->set('URI.SafeIframeRegexp', '%(rutube)|(youtube)|(vimeo)|(Coub)%');

            $def = $config->getHTMLDefinition(true);
            $def->addAttribute('iframe', 'allowfullscreen', 'Bool');

            $purifier = new \HTMLPurifier($config);
            $clean_html = $purifier->purify($this->owner->{$this->in_attribute});
            $this->owner->{$this->in_attribute} = $clean_html;
        }
    }


}