<?php
/**
 * Created by PhpStorm.
 * User: igorrozu
 * Date: 3/3/18
 * Time: 10:02 AM
 */

namespace app\components\social\clients;


use yii\authclient\clients\VKontakte;

class VK extends VKontakte
{
    /**
     * @inheritdoc
     */
    protected function initUserAttributes()
    {
        $response = $this->api('users.get.json', 'GET', [
            'fields' => implode(',', $this->attributeNames),
            'v' => '5.8',
        ]);
        $attributes = array_shift($response['response']);

        $accessToken = $this->getAccessToken();
        if (is_object($accessToken)) {
            $accessTokenParams = $accessToken->getParams();
            unset($accessTokenParams['access_token']);
            unset($accessTokenParams['expires_in']);
            $attributes = array_merge($accessTokenParams, $attributes);
        }

        return $attributes;
    }
}