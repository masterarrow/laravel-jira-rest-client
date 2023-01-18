<?php

namespace Atlassian\JiraRest\Requests\Resource;

use Atlassian\JiraRest\Requests\AbstractRequest;

class ResourceRequest extends AbstractRequest
{
    /**
     * Returns all projects visible for the currently logged in user, ie. all the projects the user has either ‘Browse
     * projects’ or ‘Administer projects’ permission.
     * If no user is logged in, it returns all projects that are visible for anonymous users.
     *
     * @see https://developer.atlassian.com/cloud/jira/platform/rest/v3/#api-rest-api-3-project-get
     *
     * @param  string $resource
     * @param  array|\Illuminate\Contracts\Support\Arrayable  $parameters
     *
     * @return \GuzzleHttp\Psr7\Response
     * @throws \Atlassian\JiraRest\Exceptions\JiraClientException
     * @throws \Atlassian\JiraRest\Exceptions\JiraNotFoundException
     * @throws \Atlassian\JiraRest\Exceptions\JiraUnauthorizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @deprecated Use search instead
     */
    public function all($resourse, $parameters = [])
    {
        return $this->execute('get', $resourse, $parameters);
    }

    /**
     * Creates a new project from a JSON representation.
     *
     * @param string $resource
     * @see https://developer.atlassian.com/cloud/jira/platform/rest/v3/#api-rest-api-3-project-post
     *
     * @param  array|\Illuminate\Contracts\Support\Arrayable  $parameters
     *
     * @return \GuzzleHttp\Psr7\Response
     * @throws \Atlassian\JiraRest\Exceptions\JiraClientException
     * @throws \Atlassian\JiraRest\Exceptions\JiraNotFoundException
     * @throws \Atlassian\JiraRest\Exceptions\JiraUnauthorizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create($resource, $parameters = [])
    {
        return $this->execute('post', $resource, $parameters);
    }

    /**
     * Returns all projects visible for the currently logged in user, ie. all the projects the user has either ‘Browse
     * projects’ or ‘Administer projects’ permission. If no user is logged in, it returns all projects that are visible
     * for anonymous users.
     *
     * @see https://developer.atlassian.com/cloud/jira/platform/rest/v3/#api-rest-api-3-project-search-get
     *
     * @param  string $resource
     * @param  array|\Illuminate\Contracts\Support\Arrayable  $parameters
     *
     * @return \GuzzleHttp\Psr7\Response
     * @throws \Atlassian\JiraRest\Exceptions\JiraClientException
     * @throws \Atlassian\JiraRest\Exceptions\JiraNotFoundException
     * @throws \Atlassian\JiraRest\Exceptions\JiraUnauthorizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function search($resource, $parameters = [])
    {
        return $this->execute('get', $resource . '/search', $parameters);
    }

    /**
     * Returns a full representation of a single project.
     *
     * @see https://developer.atlassian.com/cloud/jira/platform/rest/v3/#api-rest-api-3-project-projectIdOrKey-get
     *
     * @param  string $resource
     * @param  int|string  $resourceIdOrKey
     * @param  array|\Illuminate\Contracts\Support\Arrayable  $parameters
     *
     * @return \GuzzleHttp\Psr7\Response
     * @throws \Atlassian\JiraRest\Exceptions\JiraClientException
     * @throws \Atlassian\JiraRest\Exceptions\JiraNotFoundException
     * @throws \Atlassian\JiraRest\Exceptions\JiraUnauthorizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get($resource, $resourceIdOrKey, $parameters = [])
    {
        return $this->execute('get', "{$resource}}/{$resourceIdOrKey}", $parameters);
    }

    /**
     * Updates the details of an existing project.
     *
     * @see https://developer.atlassian.com/cloud/jira/platform/rest/v3/#api-rest-api-3-project-projectIdOrKey-put
     *
     * @param  string $resource
     * @param  int|string  $resourceIdOrKey
     * @param  array|\Illuminate\Contracts\Support\Arrayable  $parameters
     *
     * @return \GuzzleHttp\Psr7\Response
     * @throws \Atlassian\JiraRest\Exceptions\JiraClientException
     * @throws \Atlassian\JiraRest\Exceptions\JiraNotFoundException
     * @throws \Atlassian\JiraRest\Exceptions\JiraUnauthorizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update($resource, $resourceIdOrKey, $parameters = [])
    {
        return $this->execute('put', "{$resource}/{$resourceIdOrKey}", $parameters);
    }

    /**
     * Deletes an existing project.
     *
     * @see https://developer.atlassian.com/cloud/jira/platform/rest/v3/#api-rest-api-3-project-projectIdOrKey-delete
     *
     * @param  string $resource
     * @param  int|string  $resourceIdOrKey
     *
     * @return \GuzzleHttp\Psr7\Response
     * @throws \Atlassian\JiraRest\Exceptions\JiraClientException
     * @throws \Atlassian\JiraRest\Exceptions\JiraNotFoundException
     * @throws \Atlassian\JiraRest\Exceptions\JiraUnauthorizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete($resource, $resourceOrKey)
    {
        return $this->execute('delete', "{$resource}/{$resourceIdOrKey}");
    }

    /**
     * Returns a full representation of all issue types associated with a specified project, together with valid
     * statuses for each issue type.
     *
     * @see https://developer.atlassian.com/cloud/jira/platform/rest/v3/#api-rest-api-3-project-projectIdOrKey-statuses-get
     *
     * @param  string $resource
     * @param  int|string  $resourceIdOrKey
     *
     * @return \GuzzleHttp\Psr7\Response
     * @throws \Atlassian\JiraRest\Exceptions\JiraClientException
     * @throws \Atlassian\JiraRest\Exceptions\JiraNotFoundException
     * @throws \Atlassian\JiraRest\Exceptions\JiraUnauthorizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getStatuses($resource, $resourceIdOrKey)
    {
        return $this->execute('get', "project/{$resourceIdOrKey}/statuses");
    }

    /**
     * Updates project type of a single project.
     *
     * @see https://developer.atlassian.com/cloud/jira/platform/rest/v3/#api-rest-api-3-project-projectIdOrKey-type-newProjectTypeKey-put
     *
     * @param  string $resource
     * @param  int|string  $resourceIdOrKey
     * @param  string  $newResourceTypeKey
     *
     * @return \GuzzleHttp\Psr7\Response
     * @throws \Atlassian\JiraRest\Exceptions\JiraClientException
     * @throws \Atlassian\JiraRest\Exceptions\JiraNotFoundException
     * @throws \Atlassian\JiraRest\Exceptions\JiraUnauthorizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @deprecated Deprecated, this feature is no longer supported and no alternatives are available.
     */
    public function updateProjectType($resource, $resourceIdOrKey, $newResourceTypeKey)
    {
        return $this->execute('put', "{$resource}/{$resourceIdOrKey}/type/{$newResourceTypeKey}");
    }

    /**
     * Gets a notification scheme associated with the project.
     *
     * @see https://developer.atlassian.com/cloud/jira/platform/rest/v3/#api-rest-api-3-project-projectKeyOrId-notificationscheme-get
     *
     * @param  string $resource
     * @param  int|string  $resourceIdOrKey
     * @param  array|\Illuminate\Contracts\Support\Arrayable  $parameters
     *
     * @return \GuzzleHttp\Psr7\Response
     * @throws \Atlassian\JiraRest\Exceptions\JiraClientException
     * @throws \Atlassian\JiraRest\Exceptions\JiraNotFoundException
     * @throws \Atlassian\JiraRest\Exceptions\JiraUnauthorizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getNotificationScheme($resource, $resourceIdOrKey, $parameters = [])
    {
        return $this->execute('get', "{$resource}/{$resourceIdOrKey}/notificationscheme", $parameters);
    }
}
