<?php declare(strict_types=1);

namespace JSKOS;

class CountingService extends Service 
{
    private $counter=1;

    public function query(array $request=[], string $path=''): Result
    {
        return new Result([new Concept(['uri'=>'x:'.$this->counter++])]);
    }  
}
