# Jira API client for Laravel 5.4+

Perform various operations of [Jira APIs](https://developer.atlassian.com/cloud/jira/platform/rest/) with Laravel 5.4+

The aim of the package is to make it easier to communicate with the API. By default the response from the request is not altered in any way.
By creating your own implementation or by using the simple helpers provided with the package you are able to integrate Jira the way you like.

## Installation

To get the latest version of `laravel-jira-rest-client`, run the following command
```shell
composer require masterarrow/laravel-jira-rest-client
```
Do note that not all methods have been implemented yet.

Laravel 5.5 uses Package Auto-Discovery, so doesn't require you to manually add the ServiceProvider.

## Laravel 5.4:

If you don't use auto-discovery, add the ServiceProvider to the providers array in `config/app.php`
```php
<?php

'providers' => [
    // ...

    Atlassian\JiraRest\JiraRestServiceProvider::class,
],
```

Also locate the `Aliases` key in your `config/app.php` file and register the Facade:

```php
<?php

'aliases' => [
    // ...

    'Jira' => Atlassian\JiraRest\Facades\Jira::class,
],
```
Copy the package config to your local config with the publish command:
```shell
php artisan vendor:publish --provider="Atlassian\JiraRest\JiraRestServiceProvider"
```

Update the .env with your proper credentials using JIRA variables from `config/atlassian/jira.php`.

## Usage

The core of this package is a direct reflection of the Jira API mean that all request classes don't format the response you get from the API.
This desicion was made so the package is more versitile allowing users to handle the response of the requests to their own wishes. 

For example, to fetch a specific issue you could do the following
```php
<?php

$request = new \Atlassian\JiraRest\Requests\Issue\IssueRequest;
$response = $request->get('ISSUE-3');
```

All responses are an instance of `\GuzzleHttp\Psr7\Response` [Read more](http://docs.guzzlephp.org/en/stable/psr7.html) so in order to get the json response you could do the following:
```php
$response = json_decode($response->getBody(), true);
``` 
Which will return a response like seen in the [API](https://developer.atlassian.com/cloud/jira/platform/rest/#api-api-2-issue-issueIdOrKey-get)

### Parameters
Sending parameter with requests is possible via two ways
1. Using an array
2. Creating a class that implements `\Illuminate\Contracts\Support\Arrayable`

When using an array you can create a request like so:

```php
<?php

use Atlassian\JiraRest\Requests\Project;

/** @var \Atlassian\JiraRest\Requests\Project\ProjectRequest $request */
$request = app(Project\ProjectRequest::class);

$response = $request->search([
    'maxResults' => 10,
    'startAt' => 0
]);

$output = \json_decode($response->getBody()->getContents(), true);
```

You can opt for creating a class implementing `\Illuminate\Contracts\Support\Arrayable`, the `toArray` method will return the parameters for the request.
Or you could extend `\Atlassian\JiraRest\Requests\AbstractParameters` so you can 'fill' or 'override' the parameters set within the class.

Using a class can be usefull when doing requests in multiple places to make sure the same values are requests every time.

### Authentication
So Jira allows for multiple ways of authentication. By default this is basic auth.

#### Basic Auth
To use basic auth, you need to add the following to your `.env`
```
JIRA_USER=
JIRA_PASS=
```
Where the credentials are the same as the user you are logging in.

If you wish have each user be identified by their own credentials you need to set these in the config on runtime 
```php
<?php

config('atlassian.jira.auth.basic.username', 'info@example.com');
config('atlassian.jira.auth.basic.password', 'secret');
```

#### Session
Another implementation is session, but that is not (yet) implemented by this package.

#### OAuth
_To use OAuth 2.0 you need to add to your .env file:

```
JIRA_HOST=https://api.atlassian.com/ex/jira/
JIRA_AUTHENTICATION=oauth2
```

To authenticate user:

```
$url = 'https://yuor-web-site/callback-url';

$scopes = ['read:jira-user', 'read:jira-work', ...];

// $clientId and $secret you can get from .env file

$auth = new OAuthHandler($clientId, $secret, $url, $user-unique-identifier, $scopes);
$auth->authenticate();
```

After that user user will be automaticaly redirected to Jira for authentication.
In you `callback-url` route:

```
$params = $request->only('state', 'code');
$data = $this->authHandler->getAccessTokens($params);

// $data['cloudId']);
// $data['accessToken']);
// $data['refreshToken']);
// $data['expires']);
```

Get issue with OAuth 2.0:

```
$params = [
    'cloudId' => $cloudId,
    'accessToken' => $accessToken,
];

$request = new \Atlassian\JiraRest\Requests\Issue\IssueRequest;
$response = $request->get('ISSUE-3', $params);
$response = json_decode($response->getBody(), true);
```

Refresh access token:

```
$data = OAuthHandler::refreshAccessTokens($clientId, $secret,, $refreshToken);

// $data['accessToken']);
// $data['refreshToken']);
// $data['expires']);
```

### Helpers (deprecated)
Now because for the most part you don't want to spend time writing the requests yourself there are some useful helpers to get you communicating with the api.

To fetch a single issue you can use the following code:
```php
$issue = jira()->issue('ISSUE-3')->get();
```

Or use the facade if you prefer:
```php
$issue = \Jira::issue('ISSUE-3')->get();
```

_deprecating most helpers because writing helpers for all classes is too much maintenance_

### Middleware
To alter the Guzzle Client used for requests you can add 'middleware' to alter the options. 

There are multiple ways to add middleware.

#### Using the config
You can set one or more middleware using the `client_options` array in the config `config/atlassian/jira.php`

For example:
```php
<?php

'client_options' => [
    'auth' => \App\Services\Jira\Middleware\LogRequestMiddleware::class,
],
```
_These can be named or just a value_

#### Using the `addMiddleware` method
When you have an `AbstractRequest` instance you can add multiple middleware by calling `addMiddlware` like so:

```php
<?php

use Atlassian\JiraRest\Requests;

/** @var \Atlassian\JiraRest\Requests\ServerInfoRequest $request */
$request = app(Requests\ServerInfoRequest::class);
$request->addMiddleware(\App\Services\Jira\Middleware\LogRequestMiddleware::class);
```

**Impersonation**

To impersonate a user through Jira requests you must set `JIRA_IMPERSONATE=true` in your .env file. 

Once impersonation is enabled, Laravel will use the authentificated users' `name` by default. 
However, it's also possible to impersonate a user manually by sending a user's name to constructor of the middleware. To do this you would need to manually register the middleware and pass the user.


**JIRA Setup for Impersonation**
1. Follow [Jira documentation](https://developer.atlassian.com/server/jira/platform/oauth/#see-it-in-action) to generate an RSA public/private key pair.
2. Go to Jira --> Application Links (Admin)
3. Create a new link with your server url 
4. Ignore the "No response" warning
5. Enter anything in all the field and keep "Create incoming link unchecked". Jira has a weird behaviour when it comes to setting up app links. If you create your incoming link now, you won't have access to 2-Legged auth (Impersonation).
6. Click continue (ignore the warning). This should have created your new app link.
7. Edit that link (notice there are no Outgoing info even if you added dummy info at creation).
8. You may now enter all the info for OAuth and setup impersonation (Allow 2-Legged OAuth). 
