<?php
/**
 * Created by PhpStorm.
 * User: igorrozu
 * Date: 7/10/17
 * Time: 3:52 PM
 */

namespace app\components\social\attributes;


class VkAuthAttributes extends SocialAuthAttributes
{
    public function getName(): string
    {
        return $this->attributes['first_name'] ?? '';
    }

    public function getSurname(): string
    {
        return $this->attributes['last_name'] ?? '';
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
        return $this->attributes['photo_max'] ?? $this->attributes['photo_200'] ?? null;
    }


}