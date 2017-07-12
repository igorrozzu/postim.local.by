<?php
/**
 * Created by PhpStorm.
 * User: igorrozu
 * Date: 7/10/17
 * Time: 3:52 PM
 */

namespace app\components\social\attributes;


class OkAuthAttributes extends SocialAuthAttributes
{
    public function getName()
    {
        return $this->attributes['name'] ?? $this->attributes['first_name'] ?? null;
    }

    public function getEmail()
    {
        return /*$this->attributes['email'] ??*/ null;
    }

    public function getUserPhoto()
    {
        return $this->attributes['pic_3'] ?? $this->attributes['pic_2'] ??
               $this->attributes['pic_1'] ?? null;
    }


}