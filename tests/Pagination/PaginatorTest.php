<?php

namespace Illuminate\Tests\Pagination;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pagination\Paginator;
use JsonSerializable;
use PHPUnit\Framework\TestCase;

class PaginatorTest extends TestCase
{
    public function testSimplePaginatorReturnsRelevantContextInformation()
    {
        $p = new Paginator($array = ['item3', 'item4', 'item5'], 2, 2);

        $this->assertEquals(2, $p->currentPage());
        $this->assertTrue($p->hasPages());
        $this->assertTrue($p->hasMorePages());
        $this->assertEquals(['item3', 'item4'], $p->items());

        $pageInfo = [
            'per_page' => 2,
            'current_page' => 2,
            'first_page_url' => '/?page=1',
            'next_page_url' => '/?page=3',
            'prev_page_url' => '/?page=1',
            'from' => 3,
            'to' => 4,
            'data' => ['item3', 'item4'],
            'path' => '/',
        ];

        $this->assertEquals($pageInfo, $p->toArray());
    }

    public function testPaginatorRemovesTrailingSlashes()
    {
        $p = new Paginator($array = ['item1', 'item2', 'item3'], 2, 2,
                                    ['path' => 'http://website.com/test/']);

        $this->assertSame('http://website.com/test?page=1', $p->previousPageUrl());
    }

    public function testPaginatorGeneratesUrlsWithoutTrailingSlash()
    {
        $p = new Paginator($array = ['item1', 'item2', 'item3'], 2, 2,
                                    ['path' => 'http://website.com/test']);

        $this->assertSame('http://website.com/test?page=1', $p->previousPageUrl());
    }

    public function testItRetrievesThePaginatorOptions()
    {
        $p = new Paginator($array = ['item1', 'item2', 'item3'], 2, 2,
            $options = ['path' => 'http://website.com/test']);

        $this->assertSame($p->getOptions(), $options);
    }

    public function testPaginatorReturnsPath()
    {
        $p = new Paginator($array = ['item1', 'item2', 'item3'], 2, 2,
                                    ['path' => 'http://website.com/test']);

        $this->assertSame($p->path(), 'http://website.com/test');
    }

    public function testPaginatorCorrectlyJsonSerializesItsItems()
    {
        $p = new Paginator($array = [
            new class implements Arrayable, JsonSerializable {
                public function toArray()
                {
                    return ['array'];
                }

                public function jsonSerialize()
                {
                    return ['JSON'];
                }
            },
        ], 2, 2);

        $this->assertSame([['JSON']], $p->jsonSerialize()['data']);
    }
}
