<?php

namespace app\behaviors;

use dosamigos\transliterator\TransliteratorHelper;
use \yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use Yii;
use yii\helpers\Inflector;

class Slug extends Behavior
{
    public $in_attribute = 'name';
    public $out_attribute = 'slug';
    public $translit = true;

    /**
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'getSlug',
        ];
    }

    /**
     * @param $event
     */
    public function getSlug($event)
    {
        if (empty($this->owner->{$this->out_attribute})) {
            $headerWithoutQuotes = str_replace('&quot;', '', $this->owner->{$this->in_attribute});
            $this->owner->{$this->out_attribute} = $this->generateSlug($headerWithoutQuotes);
        }
    }

    /**
     * @param $slug
     * @return string
     */
    private function generateSlug($slug)
    {
        $slug = $this->slugify($slug);
        return $slug;
    }

    /**
     * @param $slug
     * @return string
     */
    private function slugify($slug)
    {
        if ($this->translit) {
            return mb_strtolower(Inflector::slug(TransliteratorHelper::process($slug), '-', true));
        } else {
            return $this->slug($slug, '-', true);
        }
    }

    /**
     * @param $string
     * @param string $replacement
     * @param bool $lowercase
     * @return string
     */
    private function slug($string, $replacement = '-', $lowercase = true)
    {
        $string = preg_replace('/[^\p{L}\p{Nd}]+/u', $replacement, $string);
        $string = trim($string, $replacement);
        return $lowercase ? strtolower($string) : $string;
    }
}