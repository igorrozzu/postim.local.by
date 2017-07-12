<?php

namespace app\components\social\attributes;

class SocialAuthAttributesFactory
{
    public function getSocialAttributes(string $socialName, array &$attributes) {
        switch ($socialName) {
            case 'twitter': return new TwitterAuthAttributes($socialName, $attributes); break;
            case 'vkontakte': return new VkAuthAttributes($socialName, $attributes); break;
            case 'odnoklassniki': return new OkAuthAttributes($socialName, $attributes); break;
            case 'facebook': return new FacebookAuthAttributes($socialName, $attributes); break;
            case 'google': return new GoogleAuthAttributes($socialName, $attributes); break;
            default: return null;
        }
    }
}