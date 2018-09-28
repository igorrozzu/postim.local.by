<?php

namespace app\components\social\attributes;

interface SocialAuthAttr
{
    public function getSocialId(): string;

    public function getSocialName(): string;

    public function getScreenName(): string;

    public function getName(): string;

    public function getSurname(): string;

    public function getEmail();

    public function getUserPhoto();
}