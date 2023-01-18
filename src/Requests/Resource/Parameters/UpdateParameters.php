<?php

namespace Atlassian\JiraRest\Requests\Project\Parameters;

use Atlassian\JiraRest\Requests\AbstractParameters;

/**
 * Class UpdateParameters
 *
 * @package Atlassian\JiraRest\Requests\Project\Parameters
 * @see https://developers.atlassian.com/cloud/jira/platform/rest/#api-api-2-project-projectIdOrKey-put
 *
 * @deprecated Use your own abstraction of \Atlassian\JiraRest\Requests\AbstractParameters or use an array instead
 */
class UpdateParameters extends AbstractParameters
{
    /**
     * Multi-value parameter defining request properties to expand in the response.
     * These are not returned by default. Allowed values:
     * - description - description of the project.
     * - projectKeys - all project keys associated with the project.
     * - lead - project lead. Note that the project lead does not always have ‘administer projects’ permission, and multiple users can have ‘administer projects’ permission for a given project.
     * - issueTypes - issue types associated with the project.
     *
     * @var string
     */
    public $expand;

    /**
     * @var array
     */
    public $body;
}