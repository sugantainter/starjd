<?php

namespace App\Socialite;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;
use Illuminate\Support\Arr;

class PinterestProvider extends AbstractProvider implements ProviderInterface
{
    protected $scopes = ['user_accounts:read'];

    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://www.pinterest.com/oauth/', $state);
    }

    protected function getTokenUrl()
    {
        return 'https://api.pinterest.com/v5/oauth/token';
    }

    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://api.pinterest.com/v5/user_account', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id'       => $user['username'],
            'nickname' => $user['username'],
            'name'     => $user['username'],
            'email'    => null, // Pinterest V5 doesn't provide email easily
            'avatar'   => $user['profile_image'] ?? null,
        ]);
    }

    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }
}
