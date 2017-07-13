<?php
/**
 * Created by PhpStorm.
 * User: igorrozu
 * Date: 7/10/17
 * Time: 3:52 PM
 */

namespace app\components\social\attributes;


class FacebookAuthAttributes extends SocialAuthAttributes
{
    public function getName(): string
    {
        return $this->attributes['first_name'] ?? $this->attributes['name'] ?? '';
    }

    public function getSurname(): string
    {
        return $this->attributes['last_name'] ?? '';
    }

    public function getEmail()
    {
        return $this->attributes['email'] ?? null;
    }

    public function getUserPhoto()
    {
        return $this->attributes['picture']['data']['url'] ?? null;
    }


}