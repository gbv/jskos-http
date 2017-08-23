<?php declare(strict_types=1);

namespace JSKOS;

class SampleService extends ConfiguredService 
{
    public function query(array $query=[], string $path=''): Result
    {
        return new Result();
    }
}

/**
 * @covers \JSKOS\ConfiguredService
 */
class ConfiguredServiceTest extends \PHPUnit\Framework\TestCase
{
    public function testService()        
    {
        $config = [
            '_uriSpace' => [
                'Concept' => [
                    'uriSpace' => 'http://example.org/concept/',
                    'notationPattern' => '/^[0-9]+$/'
                ]
            ],
            'foo' => [ 'bar' => 'doz' ]
        ];

        $service = new SampleService();
		$result = $service->queryURISpace(['notation' => '123']);
        $this->assertEquals(0, count($result));

        $service->configure($config);
		$result = $service->queryURISpace(['notation' => '123']);
		$this->assertEquals( new Concept([ 
			'uri' => 'http://example.org/concept/123',
			'notation' => ['123']
		]), $result[0] );
    }
}
