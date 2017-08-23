<?php declare(strict_types=1);

namespace JSKOS;

/**
 * @covers JSKOS\URISpaceService
 */
class URISpaceServiceTest extends \PHPUnit\Framework\TestCase
{

    public function assertEmptyResult($result) 
    {
        $this->assertTrue($result->isEmpty());
    }

    public function testService()
    {
        foreach (['/.*/',FALSE] as $notationPattern) {
            $config = [ 'Concept' => [ 'uriSpace' => 'http://example.org/' ] ];
            if ($notationPattern) {
                $config['Concept']['notationPattern'] = $notationPattern;
            }
            $service = new URISpaceService($config);
           
            $this->assertEmptyResult($service->query([]));
            $this->assertEmptyResult($service->query(['uri' => '']));
            $this->assertEmptyResult($service->query(['uri' => 'http://example.com']));
            $this->assertEmptyResult($service->query(['uri' => 'http://example.org']));

            $result = $service->query(['uri' => 'http://example.org/']);
            $this->assertInstanceOf(Concept::class, $result[0]);
            $this->assertSame('http://example.org/', $result[0]->uri);
            $this->assertNull($result[0]->notation);
     
            $result = $service->query(['uri' => 'http://example.org/foo']);
            $this->assertInstanceOf(Concept::class, $result[0]);
            $this->assertSame('http://example.org/foo', $result[0]->uri);
            $this->assertEquals(new Listing(['foo']), $result[0]->notation);

            $this->assertEmptyResult($service->query([
                'uri'      => 'http://example.org/foo', 
                'notation' => 'bar'
            ]));
        }
    }

    public function testNotation() {
        $service = new URISpaceService([
            'Concept' => [
                'uriSpace'        => 'http://example.org/',
                'notationPattern' => '/[0-9]+/',
            ]
        ]);

        $this->assertEmptyResult($service->query(['uri' => 'http://example.org/']));
        $this->assertEmptyResult($service->query(['uri' => 'http://example.org/foo']));

        $result = $service->query(['uri' => 'http://example.org/123']);
        $this->assertInstanceOf(Concept::class, $result[0]);
        $this->assertSame('http://example.org/123', $result[0]->uri);
        $this->assertEquals(new Listing(['123']), $result[0]->notation);

        // ignore empty notation
        $this->assertNotNull($service->query([
            'uri' => 'http://example.org/123', 
            'notation' => ''
        ]));

        // URI and notation don't match
        $this->assertEmptyResult($service->query([
            'uri' => 'http://example.org/123', 
            'notation' => 'foo'
        ]));

        $this->assertEmptyResult($service->query(['notation' => 'foo']));

        $result = $service->query(['notation' => '123']);
        $this->assertInstanceOf(Concept::class, $result[0]);
        $this->assertSame('http://example.org/123', $result[0]->uri);
        $this->assertEquals(new Listing(['123']), $result[0]->notation);
    }

    public function testNotationNormalizer() {
        $service = new URISpaceService([
            'Concept' => [
                'uriSpace'           => 'http://example.org/',
                'notationPattern'    => '/[QP][0-9]+/i',
                'notationNormalizer' => 'strtoupper',
            ]
        ]);
        $result = $service->query(['notation' => 'q42']);
        $this->assertSame('http://example.org/Q42', $result[0]->uri);
        $this->assertEquals(new Listing(['Q42']), $result[0]->notation);

        $result = $service->query(['uri' => 'http://example.org/q42']);
        $this->assertSame('http://example.org/q42', $result[0]->uri);
        $this->assertEquals(new Listing(['Q42']), $result[0]->notation);
     }

    # TODO: test multiple types but Concept

}
