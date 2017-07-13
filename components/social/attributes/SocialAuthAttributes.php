<?php

namespace app\components\social\attributes;

abstract class SocialAuthAttributes
{
    protected $socialName;
    protected $attributes;

    public function __construct($socialName, &$attributes)
    {
        $this->socialName = $socialName;
        $this->attributes = $attributes;
    }

    public abstract function getName(): string;
    public abstract function getSurname(): string;
    public abstract function getEmail();
    public abstract function getUserPhoto();

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