<?php declare(strict_types = 1);

namespace JSKOS;

use Http\Discovery\MessageFactoryDiscovery;

/**
 * @covers JSKOS\Server
 */
class ServerTest extends \PHPUnit\Framework\TestCase
{
    protected $messageFactory;
    protected $uriFactory;

    public function setUp()
    {
        $this->messageFactory = MessageFactoryDiscovery::find();
    }

    protected function get(array $params=[], string $path='/', array $headers=[])
    {
        $uri = "http://example.org$path?".http_build_query($params);
        return $this->messageFactory->createRequest('GET', $uri, $headers);
    }
    
    public function testResponse()
    {
        $server = new Server(new CallableService());
        $response = $server->query($this->get());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('[]', $response->getBody());

        $headers = [
            'Access-Control-Allow-Origin' => '*',
            'Content-Type' => 'application/json; charset=UTF-8',
            'X-Total-Count' => '0'
        ];
        foreach ($headers as $header => $value) {
            $this->assertEquals([$value], $response->getHeader($header));
        }
    }

    public function testJSONP()
    {
        $server = new Server(new CallableService());
        $response = $server->query($this->get(['callback'=>'abc123']));

        $this->assertEquals('/**/abc123([]);', $response->getBody());
        $this->assertEquals(
            ['application/javascript; charset=UTF-8'], 
            $response->getHeader('Content-Type'));
    }

    public function atestServerResponse()
    {
        $service = new CallableService(
            function ($query, $path) {
                return new Result();
            }
        ); 
    }


}
