<?php

namespace app\components\social\attributes;

abstract class SocialAuthAttributes implements SocialAuthAttr
{
    protected $socialName;
    protected $attributes;

    public function __construct($socialName, &$attributes)
    {
        $this->socialName = $socialName;
        $this->attributes = $attributes;
    }

    public function getSocialId(): string
    {
        return (string)$this->attributes['id'];
    }

    public function getSocialName(): string
    {
        return $this->socialName;
    }

    public function getScreenName(): string
    {
        return '';
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}