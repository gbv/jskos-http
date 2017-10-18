<?php declare(strict_types=1);

namespace JSKOS;

/**
 * Result set in response to query a JSKOS Service.
 */
class Result extends Set
{
    protected $totalCount = 0;

    /**
     * Create a new Result.
     */
    public function __construct(array $members = [])
    {
        parent::__construct($members);
        $this->setTotalCount(count($this));
    }

    /**
     * Ignores the argument to ensure that a Result is always closed.
     */
    public function setClosed(bool $closed = true)
    {
        $this->closed = true;
    }

    /**
     * Get the total number of members including other pages. May be NULL.
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * Set the total number of members including other pages. May be NULL for unknown.
     */
    public function setTotalCount(int $count)
    {
        $this->totalCount = max($count, count($this));
    }

    /**
     * Remove total count.
     */
    public function unsetTotalCount()
    {
        $this->totalCount = null;
    }

    /**
     * Append a Resource and possibly increase totalCount if needed.
     */
    public function append($resource)
    {
        parent::append($resource);
        if ($this->totalCount !== null) {
            $this->setTotalCount($this->totalCount);
        }
    }

    /**
     * Serialize with type and context fields for each member.
     */
    public function jsonLDSerialize(string $context = self::DEFAULT_CONTEXT, bool $types = NULL)
    {
        return array_map(function($m) use ($context, $types) {
            return $m->jsonLDSerialize($context, $types ?? true); 
        }, $this->members);
    }
}
