<?php declare(strict_types=1);

namespace JSKOS;

/**
 * Result set in response to query a JSKOS Service.
 */
class Result extends Set
{
    protected $totalCount=0;

    /**
     * Ignores the argument to ensure that a Result is always closed.
     */
    public function setClosed(bool $closed = true)
    {
        $this->closed = true;
    }

    /**
     * Get the total number of members including other pages.
     */
    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * Append a Resource and possibly increase totalCount.
     */
    public function append($resource)
    {
        parent::append($resource);
        $this->totalCount = max($this->totalCount, count($this));
    }
}
