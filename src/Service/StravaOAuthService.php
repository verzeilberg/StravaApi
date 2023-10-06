<?php

namespace StravaApi\Service;

class StravaOAuthService
{

    protected $config;
    /**
     * @var clientId
     */
    protected $clientId;
    /**
     * @var clientSecret
     */
    protected $clientSecret;
    /**
     * @var redirectUri
     */
    protected $redirectUri;
    /**
     * @var aprovalPrompt
     */
    protected $aprovalPrompt;
    /**
     * @var scopes
     */
    protected $scopes;

    public function __construct(
        array $config
    )
    {
        $this->config = $config;
        $this->setOptions();
    }

    /**
     * Set options into class objects
     */
    private function setOptions()
    {



        $this->clientId = $this->config['stravaSettings']['clientId'];
        $this->clientSecret = $this->config['stravaSettings']['clientSecret'];
        $this->redirectUri = $this->config['stravaSettings']['redirectUri'];
        $this->aprovalPrompt = $this->config['stravaSettings']['approval_prompt'];
        $this->scopes = $this->config['stravaSettings']['scopes'];
    }

    /**
     * Get authorize url
     * @return string
     */
    private function urlAuthorize()
    {
        return 'https://www.strava.com/oauth/authorize';
    }

    /**
     * Get token url
     * @return string
     */
    private function urlToken()
    {
        return 'https://www.strava.com/api/v3/oauth/token';
    }

    /**
     * Get authorisation link to oauth Strava client
     * @return mixed|string
     */
    public function getAuthorisationLink()
    {

        $authorisationLink = $this->urlAuthorize();
        $authorisationLink .= '?client_id=' . $this->clientId;
        $authorisationLink .= '&response_type=code';
        $authorisationLink .= '&redirect_uri=' . $this->redirectUri;
        $authorisationLink .= '&approval_prompt=' .$this->aprovalPrompt;
        $scopes = str_replace(' ', '',implode(', ', $this->scopes));
        $authorisationLink .= '&scope=' . $scopes;

        return $authorisationLink;
    }


    /**
     * Exchange token
     * @param $code
     * @return mixed
     */
    public function tokenExchange($code)
    {
        $curl = curl_init();

        $params = http_build_query([
            client_id => $this->clientId,
            client_secret => $this->clientSecret,
            code => $code,
            grant_type => 'authorization_code',
        ]);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->urlToken(),
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
            die();
        } else {
            return json_decode($response);
        }
    }

    /**
     * Refresh token
     * @param $refreshToken
     * @return mixed
     */
    public function refreshExchange($refreshToken)
    {
        $curl = curl_init();

        $params = http_build_query([
            "client_id" => $this->clientId,
            "client_secret" => $this->clientSecret,
            "grant_type" => 'refresh_token',
            "refresh_token" => $refreshToken
        ]);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->urlToken(),
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
            die;
        } else {
            return json_decode($response);
        }
    }
}
