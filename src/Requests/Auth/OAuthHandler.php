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


class OAuthHandler extends Jira
{
    /**
     * @var array
     */
    protected $scopes;

    /**
     * @var string
     */
    protected $cloudId;

    /**
     * @var League\OAuth2\Client\Token\AccessToken
     */
    protected $token;

    /**
     * Setup OAuth2 provider
     *
     * @param string $clientId          Jira cloud id
     * @param string $clientSecret      Jira ssecret
     * @param string $redirectUri       Full redirect url
     * @param string $state             User unique identifier
     * @param array $scopes             Array of Jira permissions
     */
    public function __construct($clientId, $clientSecret, $redirectUri,
                                $state = 'OPTIONAL_CUSTOM_CONFIGURED_STATE',
                                $scopes = ['read:jira-user', 'read:jira-work'])
    {
        $this->state = $state;
        $this->scopes = $scopes;

        parent::__construct([
            'clientId'          => $clientId,
            'clientSecret'      => $clientSecret,
            'redirectUri'       => $redirectUri,
        ]);

        /*$this->provider = new Jira([
            'clientId'          => $this->clientId,
            'clientSecret'      => $this->clientSecret,
            'redirectUri'       => $redirectUri,
        ]);*/
    }

    /**
     * Authenticate Jira user
     *
     * @return Redirect to Jira authentication page
     */
    public function authenticate()
    {
        // Scopes
        $options = [
            'state' => $this->state,
            'scope' => $this->scopes
        ];

        // Get an authorization code
        $authUrl = $this->getAuthorizationUrl($options);
        header('Location: ' . $authUrl);
        exit;
    }

    /**
     * Get access tokens and cloud id
     *
     * @param array $parameters     Contains 'state' and 'code' returned to redirect URL be Jira
     * @return array ['cloudId' => string,  'accessToken' => string,  'refreshToken' => string]
     * @throws JiraClientException
     */
    public function getAccessTokens($parameters = [])
    {
        if (empty($parameters['state']) || ($parameters['state'] !== $this->state)) {
            // Check given state against previously stored one to mitigate CSRF attack
            exit('Invalid state');
        } else {
            // Try to get an access token (using the authorization code grant)
            $this->token = $this->getAccessToken('authorization_code', [
                'code' => $parameters['code']
            ]);

            try {
                // Get the user's details
                $user = $this->getResourceOwner($this->token);

                // Get cloud id
                $this->cloudId = explode('/', parse_url($user->toArray()['self'])['path'])[3];

                return [
                    'cloudId' => $this->cloudId,
                    'accessToken' => $this->token->getToken(),
                    'refreshToken' => $this->token->getRefreshToken(),
                    'expires' => $this->token->getExpires(),
                ];
            } catch (RequestException $exception) {
                throw new JiraClientException($exception->getMessage(), $exception->getCode(), $exception);
            }
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

    /**
     * Return cloud id, access token and refresh token
     *
     * @return array
     */
    public function getAccessCredentials()
    {
        return [
            'cloudId' => $this->cloudId,
            'accessToken' => $this->token->getToken(),
            'refreshToken' => $this->token->getRefreshToken(),
            'expires' => $this->token->getExpires()
        ];
    }
}
