<?php

namespace LGUPlus\Centrex\Tests\Models;

use LGUPlus\Centrex\Models\ListInfo;
use PHPUnit\Framework\TestCase;

class ListInfoTest extends TestCase
{
    // 규격서 7.4 나. 응답샘플 기반
    private function sampleDatas(): array
    {
        return [
            'page'       => 1,
            'numperpage' => 10,
            'total'      => 2,
        ];
    }

    public function testFromArray(): void
    {
        $info = ListInfo::fromArray($this->sampleDatas());

        $this->assertSame(1, $info->page);
        $this->assertSame(10, $info->numPerPage);
        $this->assertSame(2, $info->total);
    }

    public function testTotalPagesCalculation(): void
    {
        // 25 건, 페이지당 10건 → 3 페이지
        $info = ListInfo::fromArray(['page' => 1, 'numperpage' => 10, 'total' => 25]);
        $this->assertSame(3, $info->totalPages);
    }

    public function testTotalPagesExact(): void
    {
        // 20 건, 페이지당 10건 → 2 페이지
        $info = ListInfo::fromArray(['page' => 1, 'numperpage' => 10, 'total' => 20]);
        $this->assertSame(2, $info->totalPages);
    }

    public function testTotalPagesOneWhenNoData(): void
    {
        // 0 건
        $info = ListInfo::fromArray(['page' => 1, 'numperpage' => 10, 'total' => 0]);
        $this->assertSame(1, $info->totalPages);
    }

    public function testTotalPagesOneWhenNumPerPageZero(): void
    {
        // numperpage = 0 이면 나누기 0 방지 → 1페이지로 처리
        $info = ListInfo::fromArray(['page' => 1, 'numperpage' => 0, 'total' => 5]);
        $this->assertSame(1, $info->totalPages);
    }

    public function testIsLastPageTrue(): void
    {
        $info = ListInfo::fromArray(['page' => 3, 'numperpage' => 10, 'total' => 25]);
        $this->assertTrue($info->isLastPage(3));
    }

    public function testIsLastPageFalse(): void
    {
        $info = ListInfo::fromArray(['page' => 1, 'numperpage' => 10, 'total' => 25]);
        $this->assertFalse($info->isLastPage(1));
        $this->assertFalse($info->isLastPage(2));
    }

    public function testIsLastPageBeyondTotal(): void
    {
        $info = ListInfo::fromArray(['page' => 1, 'numperpage' => 10, 'total' => 5]);
        $this->assertTrue($info->isLastPage(5)); // 범위 초과도 마지막으로 간주
    }

    public function testStringifiedNumericValues(): void
    {
        // 규격서 22.6 에서 numperpage, total 이 문자열로 올 수 있음
        $info = ListInfo::fromArray([
            'page'       => '1',
            'numperpage' => '10',
            'total'      => '8',
        ]);

        $this->assertSame(1, $info->page);
        $this->assertSame(10, $info->numPerPage);
        $this->assertSame(8, $info->total);
        $this->assertSame(1, $info->totalPages);
    }
}
