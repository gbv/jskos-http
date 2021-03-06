<?php declare(strict_types = 1);

namespace JSKOS;

/**
 * @covers \JSKOS\Result
 */
class ResultTest extends \PHPUnit\Framework\TestCase
{
    public function testResult()
    {
        $result = new Result([null]);
        $this->assertTrue($result->isClosed());
    }

    public function testTotalCount()
    {
        $result = new Result();
        $this->assertSame(0, $result->getTotalCount());

        foreach( [1,1,2,3] as $i )
        {
            $result[] = new Concept(['uri'=>"x:$i"]);
            $this->assertEquals($i, $result->getTotalCount());
        }

        $result->setTotalCount(2);
        $this->assertEquals(3, $result->getTotalCount());

        $result->unsetTotalCount();
        $this->assertNull($result->getTotalCount());

        $result[] = new Concept(['uri'=>'x:y']);
        $this->assertNull($result->getTotalCount());
    }
}
