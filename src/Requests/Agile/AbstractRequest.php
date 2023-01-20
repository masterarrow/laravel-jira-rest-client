<?php

namespace Atlassian\JiraRest\Requests\Agile;

abstract class AbstractRequest extends \Atlassian\JiraRest\Requests\AbstractRequest
{
    /**
     * Get the Api to call agains
     *
     * @return string
     */
    public function getApi()
    {
        if (isset($this->cloudId) /*&& isset($this->token)*/) {
            return $this->cloudId . '/rest/agile/1.0';
        }

        return '/rest/agile/1.0';
    }
}
