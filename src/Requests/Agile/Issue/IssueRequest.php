<?php

namespace Atlassian\JiraRest\Requests\Agile\Issue;

use Atlassian\JiraRest\Requests\Agile\AbstractRequest;

class IssueRequest extends AbstractRequest
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
     * Moves (ranks) issues before or after a given issue. At most 50 issues may be ranked at once.
     *
     * This operation may fail for some issues, although this will be rare.
     * In that case the 207 status code is returned for the whole response and detailed information regarding each
     * issue is available in the response body.
     *
     * If rankCustomFieldId is not defined, the default rank field will be used.
     *
     * @see https://developer.atlassian.com/cloud/jira/software/rest/#api-rest-agile-1-0-issue-rank-put
     *
     * @param  \Atlassian\JiraRest\Requests\Issue\Parameters\|array  $parameters
     *
     * @return \GuzzleHttp\Psr7\Response
     * @throws \Atlassian\JiraRest\Exceptions\JiraClientException
     * @throws \Atlassian\JiraRest\Exceptions\JiraNotFoundException
     * @throws \Atlassian\JiraRest\Exceptions\JiraUnauthorizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function rank($parameters = [])
    {
        return $this->execute('put', 'issue/rank', $parameters);
    }

    /**
     * Returns a single issue, for a given issue Id or issue key.
     * Issues returned from this resource include Agile fields, like sprint, closedSprints, flagged, and epic.
     *
     * @see https://developer.atlassian.com/cloud/jira/software/rest/#api-rest-agile-1-0-issue-issueIdOrKey-get
     *
     * @param  int|string  $issueKeyOrId  The Id or key of the requested issue.
     * @param  array|\Illuminate\Contracts\Support\Arrayable  $parameters
     *
     * @return \GuzzleHttp\Psr7\Response
     * @throws \Atlassian\JiraRest\Exceptions\JiraClientException
     * @throws \Atlassian\JiraRest\Exceptions\JiraNotFoundException
     * @throws \Atlassian\JiraRest\Exceptions\JiraUnauthorizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get($issueKeyOrId, $parameters = [])
    {
        return $this->execute('get', "issue/{$issueKeyOrId}", $parameters);
    }

    /**
     * Returns the estimation of the issue and a fieldId of the field that is used for it. boardId param is required.
     * This param determines which field will be updated on a issue.
     *
     * @see https://developer.atlassian.com/cloud/jira/software/rest/#api-rest-agile-1-0-issue-issueIdOrKey-estimation-get
     *
     * @param  int|string  $issueKeyOrId  The Id or key of the requested issue.
     * @param  int  $boardId  The id of the board required to determine which field is used for estimation.
     *
     * @return \GuzzleHttp\Psr7\Response
     * @throws \Atlassian\JiraRest\Exceptions\JiraClientException
     * @throws \Atlassian\JiraRest\Exceptions\JiraNotFoundException
     * @throws \Atlassian\JiraRest\Exceptions\JiraUnauthorizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function estimation($issueKeyOrId, $boardId)
    {
        return $this->execute('get', "issue/{$issueKeyOrId}/estimation", ['boardId' => $boardId]);
    }

    /**
     * Updates the estimation of the issue. boardId param is required.
     * This param determines which field will be updated on a issue.
     *
     * Note that this resource changes the estimation field of the issue regardless of appearance the field on the
     * screen.
     *
     * Original time tracking estimation field accepts estimation in formats like “1w”, “2d”, “3h”, “20m” or number
     * which represent number of minutes. However, internally the field stores and returns the estimation as a number
     * of seconds.
     *
     * @see https://developer.atlassian.com/cloud/jira/software/rest/#api-rest-agile-1-0-issue-issueIdOrKey-estimation-put
     *
     * @param  int|string  $issueKeyOrId  The Id or key of the requested issue.
     * @param  int  $boardId  The id of the board required to determine which field is used for estimation.
     * @param  string  $newEstimation
     *
     * @return \GuzzleHttp\Psr7\Response
     * @throws \Atlassian\JiraRest\Exceptions\JiraClientException
     * @throws \Atlassian\JiraRest\Exceptions\JiraNotFoundException
     * @throws \Atlassian\JiraRest\Exceptions\JiraUnauthorizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateEstimation($issueKeyOrId, $boardId, $newEstimation)
    {
        return $this->execute('put', "issue/{$issueKeyOrId}/estimation?boardId={$boardId}",
            ['value' => $newEstimation]);
    }
}
