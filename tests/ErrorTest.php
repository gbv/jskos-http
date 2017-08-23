<?php declare(strict_types = 1);

namespace JSKOS;

/**
 * @covers JSKOS\Error
 */
class ErrorTest extends \PHPUnit\Framework\TestCase
{
    public function testError() {
        $error = new Error(404, 'not found');
        $this->assertInstanceOf('Throwable', $error);

        $this->assertEquals(404, $error->getCode());
        $this->assertEquals('not found', $error->getMessage());
    }
}
