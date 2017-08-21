<?php declare(strict_types=1);

namespace JSKOS;

use JSKOS\PrettyJsonSerializable;

/**
 * A list of members in a possibly larger result set.
 */
class Page extends Set
{
    protected $pageNum;     /**< integer */
    protected $pageSize;    /**< integer */
    protected $total;       /**< integer */


    public function __construct(array $members=[], int $pageSize=0, int $pageNum=1, int $total=0)
    {
        parent::__construct($members);

        $this->pageNum  = $pageNum;
        $this->pageSize = $pageSize;

        $count = count($members);

        $this->total = max($total, ($pageNum-1)*$pageSize + $count);

        if ($pageSize == 0) {
            if ($pageNum == 1) {
                $pageSize = max($pageSize, $count);
            } elseif ($count > $pageSize) {
                $members = array_slice($members, 0, $pageSize);
            }
        }
    }

    public function offsetSet($offset, $object) {
        if ($object !== null) {
            parent::offsetSet($offset, $object);
        }
    }

    public function append($object)
    {
        parent::append($object);
        $this->total = max($this->total, ($this->pageNum-1)*$this->pageSize + count($this));
        if (count($this) > $this->pageSize) {
            // TODO: warn because current page is too large
        }
    }

    public function curPage()
    {
        return $this->pageNum;
    }

    public function prevPage()
    {
        return $this->pageNum > 0 ? $this->pageNum - 1 : null;
    }

    public function nextPage()
    {
        if ($this->total > $this->pageNum*$this->pageSize) {
            return $this->pageNum+1;
        } else {
            return null;
        }
    }

    public function lastPage()
    {
        return (int)($this->total / $this->pageSize);
    }

    public function totalCount(): int
    {
        return $this->total;
    }

    public function setClosed(bool $closed = true)
    {
        $this->closed = true;
    }
}
