<?php declare(strict_types = 1);
  
namespace JSKOS;

use Psr\SimpleCache\CacheInterface;

/**
 * Caches a service.
 */
class CachedService extends Service
{
    private $service;
    private $cache;

    public function __construct(Service $service, CacheInterface $cache)
    {
        $this->service = $service;
        $this->cache = $cache;
    }

    public function query(array $query=[], string $path=''): Result
    {
        ksort($query);
        $key = $path . md5(serialize($query));

        $result = $this->cache->get($key);
        if (!$result) {
            $result = $this->service->query($query, $path);
            $this->cache->set($key, $result);
        }

        return $result;
    }
}
