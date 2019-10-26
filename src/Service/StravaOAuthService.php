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

    /**
     * Set options into class objects
     */
    private function setOptions()
    {
        $this->clientId = $this->config['stravaSettings']['clientId'];
        $this->clientSecret = $this->config['stravaSettings']['clientSecret'];
        $this->redirectUri = $this->config['stravaSettings']['redirectUri'];
    }

    /**
     * Initialise Oath client
     */
    private function initialiseOath()
    {
        $options = [
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'redirectUri' => $this->redirectUri
        ];

        $this->oauth = new OAuth($options);

    }

    /**
     * Set client with acces token
     * @param null $accessToken
     */
    private function setClient($accessToken = null)
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

    /**
     * Get authorisation link to oauth Strava client
     * @return mixed
     */
    public function getAuthorisationLink()
    {

        $authoraisationLink = $this->oauth->getAuthorizationUrl([
            'scope' => [
                'read',
                'activity:read'
                // 'write',
                // 'view_private',
            ]
        ]);

        return $authoraisationLink;
    }

    /**
     * Initialise Strava client
     * @param $code code to initialise Strava client
     * @return bool|void
     */
    public function initialiseClient($code)
    {
        $token = $this->oauth->getAccessToken('authorization_code', [
            'code' => $code
        ]);
        $accessToken = $token->getToken();
        return $this->setClient($accessToken);


    }

    /**
     * Get Strava client
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }
}
