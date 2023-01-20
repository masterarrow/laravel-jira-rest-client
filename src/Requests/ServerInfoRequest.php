<?php

namespace Atlassian\JiraRest\Requests;

class ServerInfoRequest extends AbstractRequest
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
     * Returns general information about the current Jira server.
     *
     * @see https://developer.atlassian.com/cloud/jira/platform/rest/v3/#api-rest-api-3-serverInfo-get
     *
     * @param bool $doHealthCheck Deprecated.
     *
     * @return \GuzzleHttp\Psr7\Response
     * @throws \Atlassian\JiraRest\Exceptions\JiraClientException
     * @throws \Atlassian\JiraRest\Exceptions\JiraNotFoundException
     * @throws \Atlassian\JiraRest\Exceptions\JiraUnauthorizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get($doHealthCheck = false)
    {
        return $this->execute('get', 'serverInfo');
    }
}
