<?php

namespace App\Service;

use GuzzleHttp\Client;

class SyliusApi
{
    const LOCALE = 'pl_PL';

    private $client;

    /**
     * SyliusApi constructor.
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param string $uri
     * @param string $username
     * @param string $password
     * @param string $email
     * @return string
     */
    public function createAdmin($uri, $username, $password, $email)
    {
        $accessToken = $this->getAccessToken($uri);

        $response = $this->client->post($uri.'/api/v1/users/', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$accessToken}"
            ],
            \GuzzleHttp\RequestOptions::JSON => [
                "username"      => $username,
                "email"         => $email,
                "plainPassword" => $password,
                "localeCode"    => self::LOCALE,
                "enabled"       => true
            ]
        ]);


        return $response->getBody()->getContents();
    }

    private function getAccessToken($uri)
    {
        $response = $this->client->post($uri . '/api/oauth/v2/token', [
            \GuzzleHttp\RequestOptions::JSON => [
                'client_id'     => 'demo_client',
                'client_secret' => 'secret_demo_client',
                'grant_type'    => 'password',
                'username'      => 'api@example.com',
                'password'      => 'sylius-api'
            ]
        ]);

        return \GuzzleHttp\json_decode($response->getBody()->getContents())->access_token;
    }

}