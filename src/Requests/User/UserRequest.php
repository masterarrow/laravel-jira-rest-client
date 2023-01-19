<?php

namespace Atlassian\JiraRest\Requests\User;

use Atlassian\JiraRest\Requests\AbstractRequest;

class UserRequest extends AbstractRequest
{
    /**
     * @param array $options ['cloudId' => string, 'accessToken' => sting]
     */
    public function __construct($options = [])
    {
        if (isset($options['cloudId']) && isset($options['accessToken'])) {
            parent::__construct($options);
        }
    }
    
    /**
     * Returns a user.
     * This resource cannot be accessed anonymously.
     *
     * @see https://developer.atlassian.com/cloud/jira/platform/rest/v3/#api-rest-api-3-user-get
     *
     * @param  array|\\Illuminate\\Contracts\\Support\\Arrayable   $parameters
     *
     * @return \GuzzleHttp\Psr7\Response
     * @throws \Atlassian\JiraRest\Exceptions\JiraClientException
     * @throws \Atlassian\JiraRest\Exceptions\JiraNotFoundException
     * @throws \Atlassian\JiraRest\Exceptions\JiraUnauthorizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get($parameters)
    {
        return $this->execute('get', 'user', $parameters);
    }

    /**
     * Returns a list of users that match the search string and/or property and
     * are assignable to projects or issues.
     * This resource cannot be accessed anonymously.
     *
     * @see https://developer.atlassian.com/cloud/jira/platform/rest/v3/api-group-user-search/#api-rest-api-3-user-search-get
     *
     * @param  array|\\Illuminate\\Contracts\\Support\\Arrayable  $parameters
     *
     * @return \GuzzleHttp\Psr7\Response
     * @throws \Atlassian\JiraRest\Exceptions\JiraClientException
     * @throws \Atlassian\JiraRest\Exceptions\JiraNotFoundException
     * @throws \Atlassian\JiraRest\Exceptions\JiraUnauthorizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function search($parameters)
    {
        return $this->execute('get', 'user/search', $parameters);
    }

    /**
     * Returns a list of users that match the search string and/or property
     * This resource cannot be accessed anonymously.
     *
     * @see https://developer.atlassian.com/cloud/jira/platform/rest/v3/#api-rest-api-3-users-search-get
     *
     * @param  array|\\Illuminate\\Contracts\\Support\\Arrayable  $parameters
     *
     * @return \GuzzleHttp\Psr7\Response
     * @throws \Atlassian\JiraRest\Exceptions\JiraClientException
     * @throws \Atlassian\JiraRest\Exceptions\JiraNotFoundException
     * @throws \Atlassian\JiraRest\Exceptions\JiraUnauthorizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function searchAll($parameters)
    {
        return $this->execute('get', 'users/search', $parameters);
    }


}
