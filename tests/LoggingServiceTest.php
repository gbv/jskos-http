<?php declare(strict_types=1);

namespace JSKOS;

use Psr\Log\AbstractLogger;

class TestLogger extends AbstractLogger
{
    public $log=[];

    public function log($level, $message, array $context = [])
    {
        $this->log[] = $context;
    }
}

include_once 'CountingService.php';

/**
 * @covers \JSKOS\LoggingService
 */
class LoggingServiceTest extends \PHPUnit\Framework\TestCase
{
    public function testLoggingService()
    {
        $service = new CountingService();
        $logger = new TestLogger();
        $logging = new LoggingService($service, $logger);

        $query = ['foo'=>1, 'bar'=>2];
        $path = 'doz';
        $result = $logging->query($query, $path);

        $log = $logger->log;
        $this->assertEquals($query, $log[0]['query']);
        $this->assertEquals($path, $log[0]['path']);
        $this->assertEquals($result, $log[0]['result']);
     }
}
