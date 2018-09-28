<?php
/**
 * Created by PhpStorm.
 * User: igorrozu
 * Date: 7/10/17
 * Time: 3:52 PM
 */

namespace app\components\social\attributes;


class GoogleAuthAttributes extends SocialAuthAttributes
{
    public function getName(): string
    {
        return $this->attributes['name']['givenName'] ?? $this->attributes['displayName'] ?? '';
    }

    public function getSurname(): string
    {
        return $this->attributes['name']['familyName'] ?? '';
    }

    public function getEmail()
    {
        return $this->attributes['emails'][0]['value'] ?? null;
    }

    public function getUserPhoto()
    {
        if (isset($this->attributes['image']['url'])) {
            return preg_replace('/\?sz=\d+$/', '?sz=400',
                $this->attributes['image']['url']);
        }
        return null;
    }
}