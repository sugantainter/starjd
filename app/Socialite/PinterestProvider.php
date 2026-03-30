<?php

namespace App\Socialite;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;
use Illuminate\Support\Arr;

class PinterestProvider extends AbstractProvider implements ProviderInterface
{
    protected $scopes = ['user_accounts:read', 'pins:read', 'boards:read'];
    
    // Pinterest V5 uses space-separated scopes
    protected $scopeSeparator = ' ';

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
        return [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->redirectUrl,
        ];
    }

    public function getAccessTokenResponse($code)
    {
        if (empty($this->clientSecret) || $this->clientSecret === 'Add_Secret_From_Pinterest_Dashboard') {
            throw new \Exception('Pinterest Client Secret is not configured in .env');
        }

        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => $this->getTokenFields($code),
        ]);

        return json_decode($response->getBody(), true);
    }
}
