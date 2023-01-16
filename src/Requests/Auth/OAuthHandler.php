<?php

namespace Atlassian\JiraRest\Requests\Auth;

use Mrjoops\OAuth2\Client\Provider\Jira;
use Atlassian\JiraRest\JiraRestServiceProvider;
use Illuminate\Http\Request;


class OAuthHandler
{
    protected Jira $provider;
    protected $state;
    protected $scopes;

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

        $this->provider = new Jira([
            'clientId'          => $clientId,
            'clientSecret'      => $clientSecret,
            'redirectUri'       => $redirectUri,
        ]);
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

        // If we don't have an authorization code then get one
        $authUrl = $this->provider->getAuthorizationUrl($options);
        header('Location: ' . $authUrl);
        exit;
    }

    /**
     * Handle authorezation redirect
     *
     * @param Request $request
     * @return array ['cloudId' => string,  'token' => string,  'refreshToken' => string] or ['error' => string]
     */
    public function handleRedirect(Request $request)
    {
        $data = $request->only('state', 'code');

        if (empty($data['state']) || ($data['state'] !== $this->state)) {
            // Check given state against previously stored one to mitigate CSRF attack
            exit('Invalid state');
        } else {
            // Try to get an access token (using the authorization code grant)
            $token = $this->provider->getAccessToken('authorization_code', [
                'code' => $data['code']
            ]);

            // Optional: Now you have a token you can look up a users profile data
            try {
                // We got an access token, let's now get the user's details
                $user = $this->provider->getResourceOwner($token);

                $cloudId = explode('/', parse_url($user->toArray()['self'])['path'])[3];

                $request->session()->flash('cloudId', $cloudId);
                $request->session()->flash('token', $token->getToken());
                $request->session()->flash('refreshToken', $token->getRefreshToken());

                // return redirect('/connected');
                return [
                    'cloudId' => $cloudId,
                    'token' => $token->getToken(),
                    'refreshToken' => $token->getRefreshToken()
                ];
            } catch (Exception $e) {
                return ['error' => 'Failed to get tokens'];
            }
        }
    }
}
