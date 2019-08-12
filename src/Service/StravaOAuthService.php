<?php

namespace StravaApi\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Strava\API\OAuth;
use Strava\API\Exception;
use Strava\API\Client;
use Strava\API\Service\REST;

class StravaOAuthService implements StravaOAuthServiceInterface
{

    protected $config;
    protected $client;
    protected $oauth;
    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;

    public function __construct($config)
    {
        $this->config = $config;
        $this->setOptions();
        $this->initialiseOath();

    }

    private function setOptions()
    {
        $this->clientId = $this->config['stravaSettings']['clientId'];
        $this->clientSecret = $this->config['stravaSettings']['clientSecret'];
        $this->redirectUri = $this->config['stravaSettings']['redirectUri'];
    }

    private function initialiseOath()
    {
        $options = [
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'redirectUri' => $this->redirectUri
        ];

        $this->oauth = new OAuth($options);

    }


    public function getAuthorisationLink()
    {

        $authoraisationLink = $this->oauth->getAuthorizationUrl([
            // Uncomment required scopes.
            'scope' => [
                'public',
                // 'write',
                // 'view_private',
            ]
        ]);

        return $authoraisationLink;
    }

    public function initialiseClient($code)
    {
        $token = $this->oauth->getAccessToken('authorization_code', [
            'code' => $code
        ]);
        $accessToken = $token->getToken();
        return $this->setClient($accessToken);


    }

    public function setClient($accessToken = null)
    {
        try {
            $adapter = new \GuzzleHttp\Client(['base_uri' => 'https://www.strava.com/api/v3/']);
            $service = new REST($accessToken, $adapter);  // Define your user token here.
            $client = new Client($service);

            $this->client = $client;
        } catch (Exception $e) {
            print $e->getMessage();
        }
    }

    public function getClient()
    {
        return $this->client;
    }
}
