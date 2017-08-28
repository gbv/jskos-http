<?php declare(strict_types = 1);

namespace JSKOS;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Logs all queries to a Service with timestamp, duration, and result.
 */
class LoggingService extends Service
{
    protected $service;
    protected $logger;
    protected $level;

    public function __construct(Service $service, LoggerInterface $logger, $level = LogLevel::DEBUG)
    {
        $this->service = $service;
        $this->logger = $logger;
        $this->level = $level;
    }

    public function query(array $query=[], string $path=''): Result {
        $time = microtime(true);

        $result = $this->service->query($query, $path);
        $this->logger->log(
            $this->level,
            "[{time}] ".get_class($this->service)." {path}?{query} {duration}ms",
            [
                'query' => $query, 
                'path' => $path,                
                'duration' => microtime(true)-$time,
                'result' => $result,
            ]
        );

        return $result;
    }
}
