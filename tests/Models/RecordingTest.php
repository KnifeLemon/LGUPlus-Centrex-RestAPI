<?php

namespace LGUPlus\Centrex\Tests\Models;

use LGUPlus\Centrex\Models\Recording;
use PHPUnit\Framework\TestCase;

class RecordingTest extends TestCase
{
    // 규격서 7.4 나. 응답샘플 기반
    private function sampleItem(): array
    {
        return [
            'NO'        => 1,
            'FILE_NAME' => '3016-0102233xxxx_20120811183446_mix.wav',
            'SRC'       => '3016',
            'DST'       => '0102233xxxx',
            'CALLDATE'  => '20120811183446',
            'FILE_TIME' => '20120811174148',
            'FILE_SIZE' => '496KB',
        ];
    }

    public function testFromArray(): void
    {
        $r = Recording::fromArray($this->sampleItem());

        $this->assertSame('1', $r->no);
        $this->assertSame('3016-0102233xxxx_20120811183446_mix.wav', $r->fileName);
        $this->assertSame('3016', $r->src);
        $this->assertSame('0102233xxxx', $r->dst);
        $this->assertSame('20120811183446', $r->callDate);
        $this->assertSame('20120811174148', $r->fileTime);
        $this->assertSame('496KB', $r->fileSize);
    }

    public function testToArray(): void
    {
        $r   = Recording::fromArray($this->sampleItem());
        $arr = $r->toArray();

        $this->assertSame('1', $arr['no']);
        $this->assertSame('3016-0102233xxxx_20120811183446_mix.wav', $arr['fileName']);
        $this->assertArrayHasKey('callDate', $arr);
        $this->assertArrayHasKey('fileSize', $arr);
    }

    public function testMissingFieldsDefaultToEmpty(): void
    {
        $r = Recording::fromArray([]);

        $this->assertSame('', $r->no);
        $this->assertSame('', $r->fileName);
    }
}
