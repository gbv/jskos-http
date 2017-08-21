<?php declare(strict_types=1);
  
namespace JSKOS;
 
class FunctionService extends Service 
{
    protected $supportedParameters = ['uri'=>'uri'];

    private $function;
    public function __construct($function=null)
    {
        $this->function = $function;
    }
    public function query(array $query, string $path='')
    {
        $function = $this->function;
        return $function ? $function($query, $path) : new Page();
    }
}


