<?php declare(strict_types=1);

namespace JSKOS;

class MyService extends Service 
{
    protected $supportedParameters = ['notation'];
    public function query(array $request, string $path=''): Result 
    {
        return new Result([new Concept(["notation"=>$request["notation"]])]);
    }  
}

class MyOtherService extends Service 
{
    protected $supportedParameters = [];
    protected $supportedTypes = ['http://www.w3.org/2004/02/skos/core#Concept'];
    public function query(array $query, string $path=''): Result
    {
        return new Result();
    }
}

/**
 * @covers \JSKOS\Service
 */
class ServiceTest extends \PHPUnit\Framework\TestCase
{
    public function testQueryFunction()
    {
        $page = new Result();
        $method = function ($q) use ($page) {
            return $page;
        };
        $service = new CallableService($method);
        $this->assertSame($page, $service->query([]));
    }

    /*
    public function testSupportParameter()
    {
        $service = new CallableService();
        #$this->assertEquals('{?uri}', $service->uriTemplate());

        #$service->supportParameter('notation');
        #$this->assertEquals('{?notation}{?uri}', $service->uriTemplate());

        $this->assertEquals(['notation'=>'notation','uri'=>'uri'], $service->getSupportedParameters());
    }
     */

    public function testInvalidSupportParameter()
    {
        $this->expectException('DomainException');
        $service = new CallableService();
        $service->supportParameter('callback');
    }

    /*
    public function testSupportType() 
    {
        $service = new MyOtherService();
        $this->assertEquals('{?type}{?uri}', $service->uriTemplate());
    }
     */

    public function testInheritance() 
    {
        $service = new MyService();
        #$this->assertEquals('{?notation}{?uri}', $service->uriTemplate());
        $result = $service->query(['notation'=>'abc']);
        $this->assertEquals(new Concept(['notation'=>'abc']), $result[0]);
    }
}
