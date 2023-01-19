<?php

namespace Atlassian\JiraRest\Requests\Field;

use Atlassian\JiraRest\Requests\AbstractRequest;

class FieldRequest extends AbstractRequest
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
     * Returns a list of all fields, both System and Custom
     *
     * @see https://developer.atlassian.com/cloud/jira/platform/rest/#api-api-2-field-get
     *
     * @return \GuzzleHttp\Psr7\Response
     * @throws \Atlassian\JiraRest\Exceptions\JiraClientException
     * @throws \Atlassian\JiraRest\Exceptions\JiraNotFoundException
     * @throws \Atlassian\JiraRest\Exceptions\JiraUnauthorizedException
     */
    public function get()
    {
        return $this->execute('get', 'field');
    }

}
