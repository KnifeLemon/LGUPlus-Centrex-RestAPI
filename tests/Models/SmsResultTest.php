<?php

namespace LGUPlus\Centrex\Tests\Models;

use LGUPlus\Centrex\Models\SmsResult;
use PHPUnit\Framework\TestCase;

class SmsResultTest extends TestCase
{
    // 규격서 9.3 라. 구조 기반
    private function okDatas(): array
    {
        return [
            'STATUS'    => 'OK',
            'DEBUG'     => '01012341234=OK,01099998888=OK',
            'RESTCOUNT' => 3,
        ];
    }

    public function testFromArrayOk(): void
    {
        $result = SmsResult::fromArray($this->okDatas());

        $this->assertSame('OK', $result->status);
        $this->assertSame('01012341234=OK,01099998888=OK', $result->debug);
        $this->assertSame(3, $result->restCount);
    }

    public function testIsOkTrue(): void
    {
        $result = SmsResult::fromArray(['STATUS' => 'OK']);
        $this->assertTrue($result->isOk());
    }

    public function testIsOkFalseForOtherStatus(): void
    {
        $result = SmsResult::fromArray(['STATUS' => 'UNKNOW']);
        $this->assertFalse($result->isOk());
    }

    public function testRestCountCastToInt(): void
    {
        $result = SmsResult::fromArray(['RESTCOUNT' => '10']);
        $this->assertSame(10, $result->restCount);
    }

    public function testMissingFieldsDefaultToEmpty(): void
    {
        $result = SmsResult::fromArray([]);

        $this->assertSame('', $result->status);
        $this->assertSame('', $result->debug);
        $this->assertSame(0, $result->restCount);
        $this->assertFalse($result->isOk());
    }
}
