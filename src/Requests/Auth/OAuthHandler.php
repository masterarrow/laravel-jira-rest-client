<?php

namespace Atlassian\JiraRest\Requests\Auth;

use Atlassian\JiraRest\Exceptions\JiraClientException;
use Atlassian\JiraRest\Helpers\Issue;
use Atlassian\JiraRest\Requests\AbstractRequest;
use Atlassian\JiraRest\Requests\Resource\ResourceRequest;
use GuzzleHttp\Exception\RequestException;
use League\OAuth2\Client\Token\AccessToken;
use Mrjoops\OAuth2\Client\Provider\Jira;
use Atlassian\JiraRest\JiraRestServiceProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;


class OAuthHandler extends Jira
{
    /**
     * @var string
     */
    protected $state;

    /**
     * @var array
     */
    protected $scopes;

    /**
     * @var string
     */
    protected $cloudId;

    /**
     * @var AccessToken
     */
    protected $token;

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

        parent::__construct([
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
        $this->state = $state;

        // Scopes
        $options = [
            'state' => $this->state,
            'scope' => $this->scopes
        ];

        // Get an authorization code following this url
        return $this->getAuthorizationUrl($options);
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
        $this->token = $this->getAccessToken('authorization_code', [
            'code' => $code
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

    /**
     * Refresh access tokens
     *
     * @param $token
     * @param $refreshToken
     * @return array
     * @throws JiraClientException
     */
    public function refreshAccessTokens($clientId, $clientSecret, $refreshToken)
    {
        $response = Http::accept('application/json')
            ->post($this->getRefreshTokenUrl(), [
                'grant_type' => 'refresh_token',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'refresh_token' => $refreshToken
            ]);

        if ($response->status() === 200) {
            $result = json_decode($response->body(), true);

            $this->token = new AccessToken($result);

            return [
                'accessToken' => $this->token->getToken(),
                'refreshToken' => $this->token->getRefreshToken(),
                'expires' => $this->token->getExpires(),
            ];
        } else {
            throw new JiraClientException('Cannot get a new access token', $response->status());
        }
    }

    /**
     * @return string
     */
    public function getRefreshTokenUrl()
    {
        return 'https://auth.atlassian.com/oauth/token';
    }

    /**
     * Get authenticated user information
     *
     * @return \League\OAuth2\Client\Provider\ResourceOwnerInterface
     */
    public function getMe()
    {
        $response = Http::accept('application/json')
            ->withToken($this->token->getToken())
            ->get('https://api.atlassian.com/me');

        if ($response->status() === 200) {
            return json_decode($response->body(), true);
        } else {
            throw new JiraClientException('Cannot get current user', $response->status());
        }
    }

    /**
     * Make a reguest to the resource URL
     *
     * @param string $method        GET, POST, ...
     * @param string $resource      Resource URL (example 'issue/TEST-1')
     * @param array $parameters
     * @return mixed|array
     *
     * @throws JiraClientException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendRequest($method, $resource, $parameters = [])
    {
        $url = $this->getRequestUri() . $resource;

        $response = Http::accept('application/json')
            ->withToken($this->token->getToken())
            ->send($method, $url, $parameters);

        if ($response->status() === 200) {
            return json_decode($response->body(), true);
        } else {
            throw new JiraClientException($response->body(), $response->status());
        }
    }

    /**
     * @return string
     */
    private function getRequestUri()
    {
        return config('atlassian.jira.host') . $this->cloudId . '/rest/api/2/';
    }

    /**
     * Get cloud id and access token
     *
     * @return array
     */
    public function getAuthParams()
    {
        return [
            'cloudId' => $this->cloudId,
            'accessToken' => $this->token->getToken()
        ];
    }

    /**
     * Project
     *
     * @return \Atlassian\JiraRest\Requests\Project\ProjectRequest
     */
    public function project()
    {
        return new \Atlassian\JiraRest\Requests\Project\ProjectRequest($this->getAuthParams());
    }

    /**
     * Dashboard
     *
     * @return \Atlassian\JiraRest\Requests\Dashboard\DashboardRequest
     */
    public function dashboard()
    {
        return new \Atlassian\JiraRest\Requests\Dashboard\DashboardRequest($this->getAuthParams());
    }

    /**
     * Issue
     *
     * @return \Atlassian\JiraRest\Requests\Issue\IssueRequest
     */
    public function issue()
    {
        return new \Atlassian\JiraRest\Requests\Issue\IssueRequest($this->getAuthParams());
    }

    /**
     * Field
     *
     * @return \Atlassian\JiraRest\Requests\Field\FieldRequest
     */
    public function field()
    {
        return new \Atlassian\JiraRest\Requests\Field\FieldRequest($this->getAuthParams());
    }

    /**
     * Group
     *
     * @return \Atlassian\JiraRest\Requests\Group\GroupRequest
     */
    public function group()
    {
        return new \Atlassian\JiraRest\Requests\Group\GroupRequest($this->getAuthParams());
    }

    /**
     * User
     *
     * @return \Atlassian\JiraRest\Requests\User\UserRequest
     */
    public function user()
    {
        return new \Atlassian\JiraRest\Requests\User\UserRequest($this->getAuthParams());
    }

    /**
     * Workflow
     *
     * @return \Atlassian\JiraRest\Requests\Workflow\WorkflowRequest
     */
    public function workflow()
    {
        return new \Atlassian\JiraRest\Requests\Workflow\WorkflowRequest($this->getAuthParams());
    }

    /**
     * Any Jira resource
     *
     * @return ResourceRequest
     */
    public function resource()
    {
        return new \Atlassian\JiraRest\Requests\Resource\ResourceRequest($this->getAuthParams());
    }

    /**
     * Agile Board
     *
     * @return \Atlassian\JiraRest\Requests\Agile\Board\BoardRequest
     */
    public function board()
    {
        return new \Atlassian\JiraRest\Requests\Agile\Board\BoardRequest($this->getAuthParams());
    }

    /**
     * Agile Backlog
     *
     * @return \Atlassian\JiraRest\Requests\Agile\Backlog\BacklogRequest
     */
    public function backlog()
    {
        return new \Atlassian\JiraRest\Requests\Agile\Backlog\BacklogRequest($this->getAuthParams());
    }

    /**
     * Agile Epic
     *
     * @return \Atlassian\JiraRest\Requests\Agile\Epic\EpicRequest
     */
    public function epic()
    {
        return new \Atlassian\JiraRest\Requests\Agile\Epic\EpicRequest($this->getAuthParams());
    }

    /**
     * Agile Issue
     *
     * @return \Atlassian\JiraRest\Requests\Agile\Issue\IssueRequest
     */
    public function agileIssue()
    {
        return new \Atlassian\JiraRest\Requests\Agile\Issue\IssueRequest($this->getAuthParams());
    }

    /**
     * Agile Sprint
     *
     * @return \Atlassian\JiraRest\Requests\Agile\Sprint\SprintRequest
     */
    public function sprint()
    {
        return new \Atlassian\JiraRest\Requests\Agile\Sprint\SprintRequest($this->getAuthParams());
    }

    /**
     * Builds
     *
     * @return \Atlassian\JiraRest\Requests\Agile\BuildsRequest
     */
    public function builds()
    {
        return new \Atlassian\JiraRest\Requests\Agile\BuildsRequest($this->getAuthParams());
    }

    /**
     * Deployments
     *
     * @return \Atlassian\JiraRest\Requests\Agile\DeploymentsRequest
     */
    public function deployments()
    {
        return new \Atlassian\JiraRest\Requests\Agile\DeploymentsRequest($this->getAuthParams());
    }

    /**
     * Development Information
     *
     * @return \Atlassian\JiraRest\Requests\Agile\DevelopmentInformationRequest
     */
    public function developmentInformation()
    {
        return new \Atlassian\JiraRest\Requests\Agile\DevelopmentInformationRequest($this->getAuthParams());
    }

    /**
     * Feature Flags
     *
     * @return \Atlassian\JiraRest\Requests\Agile\FeatureFlagsRequest
     */
    public function featureFlags()
    {
        return new \Atlassian\JiraRest\Requests\Agile\FeatureFlagsRequest($this->getAuthParams());
    }

    /**
     * Server Info
     *
     * @return \Atlassian\JiraRest\Requests\ServerInfoRequest
     */
    public function serverInfo()
    {
        return new \Atlassian\JiraRest\Requests\ServerInfoRequest($this->getAuthParams());
    }

    /**
     * Set authentication parameters
     *
     * @param array|\Illuminate\Contracts\Support\Arrayable $parameters ['cloudId', 'accessToken', 'refreshToken', 'expires']
     *
     * @return bool
     */
    public function setAuthParameters($parameters = [])
    {
        $this->cloudId = $parameters['cloudId'] ?? $this->cloudId;
        $this->token = new AccessToken([
            'access_token' => $parameters['accessToken'],
            'refresh_token' => $parameters['refreshToken'],
            'expires_in' => $parameters['expires']
        ]);
    }
}
