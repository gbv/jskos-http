<?php declare(strict_types=1);

namespace JSKOS;

/**
 * @covers \JSKOS\Page
 */
class PageTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $page = new Page();
        $this->assertEquals(0, $page->totalCount());
        $this->assertEquals(1, $page->curPage());
        $this->assertEquals("$page", "[]");
    }

    public function testContent()
    {
        $page = new Page();
        $page[] = new Concept(['uri'=>'x:1']);

        $this->assertEquals(1, $page->totalCount());
        $this->assertEquals(1, count($page));
    }
}
