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
    public function testConstruct() {
        $httpClient = new MockClient();

        $httpClient = new HttpMethodsClient(
            $httpClient,
            MessageFactoryDiscovery::find()
        );

        $client = new Client('http://example.org/');

        $page = $client->query([]);
        $this->assertEquals(new Page(), $page);
    }
}
