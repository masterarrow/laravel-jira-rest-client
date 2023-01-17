<?php

namespace Atlassian\JiraRest\Requests\Auth;

use Atlassian\JiraRest\Exceptions\JiraClientException;
use Atlassian\JiraRest\Requests\AbstractRequest;
use GuzzleHttp\Exception\RequestException;
use League\OAuth2\Client\Token\AccessToken;
use Mrjoops\OAuth2\Client\Provider\Jira;
use Atlassian\JiraRest\JiraRestServiceProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;


class OAuthHandler
{
    /**
     * @var Jira
     */
    protected $provider;

    /**
     * @var string
     */
    protected $state;

    /**
     * @var array
     */
    protected $scopes;

    /**
     * Setup OAuth2 provider
     *
     * @param string $clientId          Jira client id
     * @param string $clientSecret      Jira ssecret
     * @param string $redirectUri       Full redirect url
     * @param string $state             User unique identifier
     * @param array $scopes             Array of Jira permissions
     */
    public function __construct($clientId, $clientSecret, $redirectUri, $scopes = ['read:jira-user', 'read:jira-work'])
    {
        $this->scopes = $scopes;

        $this->provider = new Jira([
            'clientId'          => $clientId,
            'clientSecret'      => $clientSecret,
            'redirectUri'       => $redirectUri,
        ]);
    }

    /**
     * Authenticate Jira user
     *
         * @return string   Authorization URL
     */
    public function authorizationUrl($state = 'OPTIONAL_CUSTOM_CONFIGURED_STATE')
    {
        // Scopes
        $options = [
            'state' => $state,
            'scope' => $this->scopes
        ];

        // Get an authorization code following this url
        return $this->provider->getAuthorizationUrl($options);
    }

    /**
     * Get access tokens and cloud id
     *
     * @param array $code           Code returned to redirect URL by Jira
     * @return array ['cloudId' => string,  'accessToken' => string,  'refreshToken' => string, 'expires' => int]
     * @throws JiraClientException
     */
    public function getAccessTokens($code)
    {
        // Try to get an access token (using the authorization code grant)
        $token = $this->provider->getAccessToken('authorization_code', [
            'code' => $code
        ]);

        try {
            // Get the user's details
            $user = $this->provider->getResourceOwner($token);

            // Get cloud id
            $cloudId = explode('/', parse_url($user->toArray()['self'])['path'])[3];

            return [
                'cloudId' => $cloudId,
                'accessToken' => $token->getToken(),
                'refreshToken' => $token->getRefreshToken(),
                'expires' => $token->getExpires(),
            ];
        } catch (RequestException $exception) {
            throw new JiraClientException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * Refresh access tokens
     *
     * @param $token
     * @param $refreshToken
     * @return array
     * @throws JiraClientException
     */
    public static function refreshAccessTokens($clientId, $clientSecret, $refreshToken)
    {
        $response = Http::accept('application/json')
            ->post(OAuthHandler::getRefreshTokenUrl(), [
                'grant_type' => 'refresh_token',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'refresh_token' => $refreshToken
            ]);

        if ($response->status() === 200) {
            $result = json_decode($response->body(), true);

            $token = new AccessToken($result);

            return [
                'accessToken' => $token->getToken(),
                'refreshToken' => $token->getRefreshToken(),
                'expires' => $token->getExpires(),
            ];
        } else {
            throw new JiraClientException('Cannot get a new access token', $response->getStatusCode());
        }
    }

    /**
     * @return string
     */
    public static function getRefreshTokenUrl()
    {
        return 'https://auth.atlassian.com/oauth/token';
    }
}
