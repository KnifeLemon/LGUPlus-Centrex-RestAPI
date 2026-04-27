<?php

namespace LGUPlus\Centrex\Tests\Models;

use LGUPlus\Centrex\Models\AuthCodeResult;
use PHPUnit\Framework\TestCase;

class AuthCodeResultTest extends TestCase
{
    // 규격서 25.4 나.(1) 정상 응답 샘플
    private function sampleDatas(): array
    {
        return [
            'STATUS'    => '인증코드 해당 단말기 메시지로 전송 완료되었습니다. 유효시간은 5분입니다.',
            'SESSIONID' => '7c87a95ea6e9f3ece019e21dcc0bed5c',
            'TRYCNT'    => '1',
        ];
    }

    public function testFromArray(): void
    {
        $result = AuthCodeResult::fromArray($this->sampleDatas());

        $this->assertSame('7c87a95ea6e9f3ece019e21dcc0bed5c', $result->sessionId);
        $this->assertSame(1, $result->tryCnt);
        $this->assertStringContainsString('5분', $result->status);
    }

    public function testTryCntCastToInt(): void
    {
        $result = AuthCodeResult::fromArray(['TRYCNT' => '3']);
        $this->assertSame(3, $result->tryCnt);
    }

    public function testMissingFieldsDefaultToEmpty(): void
    {
        $result = AuthCodeResult::fromArray([]);

        $this->assertSame('', $result->status);
        $this->assertSame('', $result->sessionId);
        $this->assertSame(0, $result->tryCnt);
    }
}
