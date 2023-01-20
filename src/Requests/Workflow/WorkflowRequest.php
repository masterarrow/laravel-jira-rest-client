<?php

namespace Atlassian\JiraRest\Requests\Workflow;

use Atlassian\JiraRest\Requests\AbstractRequest;

class WorkflowRequest extends AbstractRequest
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
     * Returns a list of all statuses associated with workflows.
     *
     * @see https://developer.atlassian.com/cloud/jira/platform/rest/v3/#api-rest-api-3-status-get
     *
     * @return \GuzzleHttp\Psr7\Response
     * @throws \Atlassian\JiraRest\Exceptions\JiraClientException
     * @throws \Atlassian\JiraRest\Exceptions\JiraNotFoundException
     * @throws \Atlassian\JiraRest\Exceptions\JiraUnauthorizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAllStatuses()
    {
        return $this->execute('get', 'status');
    }

    /**
     * Get the Api to call agains
     *
     * @return string
     */
    public function getApi()
    {
        if (isset($this->cloudId) /*&& isset($this->token)*/) {
            return $this->cloudId . '/rest/api/3';
        }

        return '/rest/api/3';
    }
}
