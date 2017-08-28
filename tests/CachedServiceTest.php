<?php declare(strict_types=1);

namespace JSKOS;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Cache\Bridge\SimpleCache\SimpleCacheBridge;

include_once 'CountingService.php';

/**
 * @covers \JSKOS\CachedService
 */
class CachedServiceTest extends \PHPUnit\Framework\TestCase
{
    public function testCaching()
    {
        $counting = new CountingService();
        $cache = new SimpleCacheBridge(new ArrayCachePool());
        $service = new CachedService($counting, $cache);

        $expect = new Result([new Concept(['uri'=>'x:1'])]);
        $this->assertEquals($expect, $service->query());
        $this->assertEquals($expect, $service->query());

        $expect[0]->uri = 'x:2';
        $this->assertEquals($expect, $counting->query());
    }
}
