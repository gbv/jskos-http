<?php declare(strict_types=1);

namespace JSKOS;

use Http\Client\Common\HttpMethodsClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;

use Http\Mock\Client as MockClient;

/**
 * @covers JSKOS\Client
 */
class ClientTest extends \PHPUnit\Framework\TestCase
{
    private $responseFactory;

    public function setUp() {
        $this->responseFactory = MessageFactoryDiscovery::find();
        $this->mockClient = new MockClient();
    }

    private function mockResponse($code, $headers=[], $body=null) {
        $this->mockClient->addResponse(
            $this->responseFactory->createResponse($code, null, $headers, $body)
        );
    }

    /**
     * @dataProvider provideExamples
     */
    public function testExamples($body, $expect) {
        $client = new Client('http://example.org/', $this->mockClient);

        $this->mockResponse(200, [], $body);
        $result = $client->query();
        $this->assertEquals(new Result($expect), $result);
    } 

    public function provideExamples() {
        return [ 
            [ "\n\n[]", [] ],
            [ '[{}]', [new Concept()] ],
            [ '[{"uri":"x:1","ignore":2}]', [new Concept(['uri'=>'x:1'])] ],
            [ 
                '[{"type":["http://www.w3.org/2004/02/skos/core#ConceptScheme"]}]',
                [new ConceptScheme()] 
            ]
        ];
    }

    /**
     * @dataProvider provideErrors
     */
    public function testErrors($args, $message) {
        $client = new Client('http://example.org/', $this->mockClient);

        $this->mockResponse(...$args);
        $this->expectException('JSKOS\Error');
        $this->expectExceptionCode(502);
        $this->expectExceptionMessage($message);
        $client->query();
    }

    public function provideErrors() {
        return [ 
            [ [200, [], '?!'], 'Failed to parse JSON' ],
            [ [200, [], '{}'], 'Failed to parse JSON array' ],
            [ [404, [], ''], 'Unsuccessful HTTP response' ],
            #[ [200, [], '1'], 'Invalid JSON response' ]
        ];
    }
}
