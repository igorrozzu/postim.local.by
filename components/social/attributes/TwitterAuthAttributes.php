<?php
/**
 * Created by PhpStorm.
 * User: igorrozu
 * Date: 7/10/17
 * Time: 3:52 PM
 */

namespace app\components\social\attributes;


class TwitterAuthAttributes extends SocialAuthAttributes
{
    public function getName(): string
    {
        return $this->attributes['name'] ?? '';
    }

    public function getSurname(): string
    {
        return '';
    }

    public function getScreenName(): string
    {
        return $this->attributes['screen_name'] ?? '';
    }

    public function getEmail()
    {
        return $this->attributes['email'] ?? null;
    }


    public function getUserPhoto()
    {
        return $this->attributes['profile_image_url_https'] ?? null;
    }
}