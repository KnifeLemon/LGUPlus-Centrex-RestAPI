<?php

namespace LGUPlus\Centrex\Tests\Models;

use LGUPlus\Centrex\Models\CallHistory;
use PHPUnit\Framework\TestCase;

class CallHistoryTest extends TestCase
{
    // 규격서 8.4 나. 응답샘플 기반
    private function sampleItem(): array
    {
        return [
            'NO'       => 1,
            'TIME'     => '2012-10-05 17:30:30',
            'SRC'      => '3010',
            'DST'      => '0104518****',
            'DURATION' => '0',
            'BILLSEC'  => '0',
            'STATUS'   => 'FAIL',
            'KIND'     => '2',
        ];
    }

    public function testFromArray(): void
    {
        $h = CallHistory::fromArray($this->sampleItem());

        $this->assertSame('1', $h->no);
        $this->assertSame('2012-10-05 17:30:30', $h->time);
        $this->assertSame('3010', $h->src);
        $this->assertSame('0104518****', $h->dst);
        $this->assertSame('0', $h->duration);
        $this->assertSame('0', $h->billSec);
        $this->assertSame('FAIL', $h->status);
        $this->assertSame('2', $h->kind);
    }

    public function testKindValues(): void
    {
        foreach (['0', '1', '2', '3'] as $kind) {
            $h = CallHistory::fromArray(['KIND' => $kind]);
            $this->assertSame($kind, $h->kind);
        }
    }

    public function testMissingFieldsDefaultToEmpty(): void
    {
        $h = CallHistory::fromArray([]);

        $this->assertSame('', $h->no);
        $this->assertSame('', $h->src);
    }
}
