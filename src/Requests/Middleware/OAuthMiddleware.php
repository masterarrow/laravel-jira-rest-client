<?php

namespace Atlassian\JiraRest\Requests\Middleware;

use Atlassian\JiraRest\JiraRestServiceProvider as Provider;
use Closure;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

use kamermans\OAuth2\GrantType\ClientCredentials;
use kamermans\OAuth2\OAuth2Subscriber;

class OAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param $options
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($options, Closure $next)
    {
        /*$stack = HandlerStack::create();

        if (config(Provider::CONFIG_KEY.'.auth.oauth.impersonate')) {
            $userId = null;

            if ($options['userId']) {
                $userId = $options['userId'];
            } else if (auth()->check()) {
                $userId = auth()->user()->name;
            }
            if ($userId) {
                $stack->push(new ImpersonateMiddleware($userId));
            }
        }

        $token = '';
        $tokenSecret = '';

        // For a request-token request these need to be empty to prevent a 401
        if (! session()->pull(Provider::CONFIG_KEY.'.oauth.initial-request', false)) {
            // Check if the session has values
            if (session()->has(Provider::CONFIG_KEY.'.oauth.tokens')) {
                $token = session()->get(Provider::CONFIG_KEY.'.oauth.tokens.oauth_token');
                $tokenSecret = session()->get(Provider::CONFIG_KEY.'.oauth.tokens.oauth_token_secret');
            } else {
                // Get the default tokens from the config
                $token = config(Provider::CONFIG_KEY.'.auth.oauth.oauth_token', '');
                $tokenSecret = config(Provider::CONFIG_KEY.'.auth.oauth.oauth_token_secret', '');
            }
        }

        $middleware = new Oauth1([
            'consumer_key'           => config(Provider::CONFIG_KEY.'.auth.oauth.consumer_key'),
            'consumer_secret'        => config(Provider::CONFIG_KEY.'.auth.oauth.consumer_secret'),
            'token'                  => $token,
            'token_secret'           => $tokenSecret,
            'private_key_file'       => config(Provider::CONFIG_KEY.'.auth.oauth.private_key'),
            'private_key_passphrase' => config(Provider::CONFIG_KEY.'.auth.oauth.private_key_passphrase'),
            'signature_method'       => Oauth1::SIGNATURE_METHOD_RSA,
        ]);

        $stack->push($middleware);

        $options['auth'] = 'oauth';
        $options['handler'] = $stack;

        return $next($options);*/

        $stack = HandlerStack::create();

        $scopes = ['offline_access', 'read:me', 'read:account', 'report:personal-data', 'read:jira-user',
            'read:jira-work', 'write:jira-work', 'manage:jira-project', 'manage:jira-configuration',
            'manage:jira-webhook', 'manage:jira-data-provider'];

        $reauth_config = [
            "client_id" => env('JIRA_CLIENT_ID'),
            "client_secret" => env('JIRA_SECRET'),
            "scope" => $scopes,
            "state" => time()
        ];

        $middleware = new OAuth2Subscriber($reauth_config);

        $stack->push($middleware);

        $options['auth'] = 'oauth2';
        $options['handler'] = $stack;

        return $next($options);

        /*
        // Authorization client - this is used to request OAuth access tokens
        $reauth_client = new GuzzleHttp\Client([
            // URL for access_token request
            'base_url' => 'http://some_host/access_token_request_url',
        ]);
        $reauth_config = [
            "client_id" => "your client id",
            "client_secret" => "your client secret",
            "scope" => "your scope(s)", // optional
            "state" => time(), // optional
        ];
        $grant_type = new ClientCredentials($reauth_client, $reauth_config);
        $oauth = new OAuth2Subscriber($grant_type);

        // This is the normal Guzzle client that you use in your application
        $client = new GuzzleHttp\Client([
            'auth' => 'oauth',
        ]);
        $client->getEmitter()->attach($oauth);
        $response = $client->get('http://somehost/some_secure_url');

        echo "Status: ".$response->getStatusCode()."\n";
        */
    }
}
