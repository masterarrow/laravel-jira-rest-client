<?php

namespace Atlassian\JiraRest\Requests\Agile\Epic;

use Atlassian\JiraRest\Requests\Agile\AbstractRequest;

class EpicRequest extends AbstractRequest
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
     * Returns all issues that do not belong to any epic.
     * This only includes issues that the user has permission to view.
     * Issues returned from this resource include Agile fields, like sprint, closedSprints, flagged, and epic.
     * By default, the returned issues are ordered by rank.
     *
     * @see https://developer.atlassian.com/cloud/jira/software/rest/#api-rest-agile-1-0-epic-none-issue-get
     *
     * @param  array|\Illuminate\Contracts\Support\Arrayable  $parameters
     *
     * @return \GuzzleHttp\Psr7\Response
     * @throws \Atlassian\JiraRest\Exceptions\JiraClientException
     * @throws \Atlassian\JiraRest\Exceptions\JiraNotFoundException
     * @throws \Atlassian\JiraRest\Exceptions\JiraUnauthorizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function issuesWithoutEpic($parameters = [])
    {
        return $this->issues('none', $parameters);
    }

    /**
     * Removes issues from epics.
     * The user needs to have the edit issue permission for all issue they want to remove from epics.
     * The maximum number of issues that can be moved in one operation is 50.
     *
     * @see https://developer.atlassian.com/cloud/jira/software/rest/#api-rest-agile-1-0-epic-none-issue-post
     *
     * @param  array  $issues  A list of both issue id or key are accepted.
     *
     * @return \GuzzleHttp\Psr7\Response
     * @throws \Atlassian\JiraRest\Exceptions\JiraClientException
     * @throws \Atlassian\JiraRest\Exceptions\JiraNotFoundException
     * @throws \Atlassian\JiraRest\Exceptions\JiraUnauthorizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function removeIssuesFromEpic(array $issues)
    {
        return $this->execute('post', 'epic/none/issue', ['issues' => $issues]);
    }

    /**
     * Returns the epic for a given epic Id.
     * This epic will only be returned if the user has permission to view it.
     *
     * @see https://developer.atlassian.com/cloud/jira/software/rest/#api-rest-agile-1-0-epic-epicIdOrKey-get
     *
     * @param  int|string  $epicIdOrKey  The id or key of the requested epic.
     *
     * @return \GuzzleHttp\Psr7\Response
     * @throws \Atlassian\JiraRest\Exceptions\JiraClientException
     * @throws \Atlassian\JiraRest\Exceptions\JiraNotFoundException
     * @throws \Atlassian\JiraRest\Exceptions\JiraUnauthorizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get($epicIdOrKey)
    {
        return $this->execute('get', "epic/{$epicIdOrKey}");
    }

    /**
     * Performs a partial update of the epic.
     * A partial update means that fields not present in the request JSON will not be updated.
     *
     * @see https://developer.atlassian.com/cloud/jira/software/rest/#api-rest-agile-1-0-epic-epicIdOrKey-post
     *
     * @param  int|string  $epicIdOrKey  The id or key of the epic to update.
     * @param  array|\Illuminate\Contracts\Support\Arrayable  $parameters
     *
     * @return \GuzzleHttp\Psr7\Response
     * @throws \Atlassian\JiraRest\Exceptions\JiraClientException
     * @throws \Atlassian\JiraRest\Exceptions\JiraNotFoundException
     * @throws \Atlassian\JiraRest\Exceptions\JiraUnauthorizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function partialUpdate($epicIdOrKey, $parameters = [])
    {
        return $this->execute('post', "epic/{$epicIdOrKey}", $parameters);
    }

    /**
     * Returns all issues that belong to the epic, for the given epic Id.
     * This only includes issues that the user has permission to view.
     * Issues returned from this resource include Agile fields, like sprint, closedSprints, flagged, and epic.
     * By default, the returned issues are ordered by rank.
     *
     * @see https://developer.atlassian.com/cloud/jira/software/rest/#api-rest-agile-1-0-epic-epicIdOrKey-issue-get
     *
     * @param  int|string  $epicIdOrKey  The id or key of the epic that contains the requested issues.
     * @param  array|\Illuminate\Contracts\Support\Arrayable  $parameters
     *
     * @return \GuzzleHttp\Psr7\Response
     * @throws \Atlassian\JiraRest\Exceptions\JiraClientException
     * @throws \Atlassian\JiraRest\Exceptions\JiraNotFoundException
     * @throws \Atlassian\JiraRest\Exceptions\JiraUnauthorizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function issues($epicIdOrKey, $parameters = [])
    {
        return $this->execute('get', "epic/{$epicIdOrKey}/issue", $parameters);
    }

    /**
     * Moves issues to an epic, for a given epic id.
     * Issues can be only in a single epic at the same time.
     * That means that already assigned issues to an epic, will not be assigned to the previous epic anymore.
     * The user needs to have the edit issue permission for all issue they want to move and to the epic.
     * The maximum number of issues that can be moved in one operation is 50.
     *
     * @see https://developer.atlassian.com/cloud/jira/software/rest/#api-rest-agile-1-0-epic-epicIdOrKey-issue-post
     *
     * @param  int|string  $epicIdOrKey  The id or key of the epic that you want to assign issues to.
     * @param  array  $issues  A list of both issue id or key are accepted.
     *
     * @return \GuzzleHttp\Psr7\Response
     * @throws \Atlassian\JiraRest\Exceptions\JiraClientException
     * @throws \Atlassian\JiraRest\Exceptions\JiraNotFoundException
     * @throws \Atlassian\JiraRest\Exceptions\JiraUnauthorizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function moveIssues($epicIdOrKey, array $issues)
    {
        return $this->execute('post', "epic/{$epicIdOrKey}/issue", ['issues' => $issues]);
    }

    /**
     * Moves (ranks) an epic before or after a given epic.
     *
     * If rankCustomFieldId is not defined, the default rank field will be used.
     *
     * @see https://developer.atlassian.com/cloud/jira/software/rest/#api-rest-agile-1-0-epic-epicIdOrKey-rank-put
     *
     * @param  int|string  $epicIdOrKey  The id or key of the epic to rank.
     * @param  array|\Illuminate\Contracts\Support\Arrayable  $parameters
     *
     * @return \GuzzleHttp\Psr7\Response
     * @throws \Atlassian\JiraRest\Exceptions\JiraClientException
     * @throws \Atlassian\JiraRest\Exceptions\JiraNotFoundException
     * @throws \Atlassian\JiraRest\Exceptions\JiraUnauthorizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function rankEpics($epicIdOrKey, $parameters = [])
    {
        return $this->execute('put', "epic/{$epicIdOrKey}/rank", $parameters);
    }
}
