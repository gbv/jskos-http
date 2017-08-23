<?php declare(strict_types = 1);
  
namespace JSKOS;
 
/**
 * Wrap Callable as JSKOS Service.
 */
class CallableService extends Service
{
    private $callable;

    public function __construct(Callable $callable=null)
    {
        $this->callable = $callable;
    }

    public function query(array $query=[], string $path=''): Result
    {
        $call = $this->callable;
        return $call ? $call($query, $path) : new Result();
    }
}
