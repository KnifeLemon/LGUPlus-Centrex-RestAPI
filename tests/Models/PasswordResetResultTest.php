<?php

namespace LGUPlus\Centrex\Tests\Models;

use LGUPlus\Centrex\Models\PasswordResetResult;
use PHPUnit\Framework\TestCase;

class PasswordResetResultTest extends TestCase
{
    // 규격서 26.4 나.(1) 정상 응답 샘플
    private function successDatas(): array
    {
        return [
            'STATUS'    => ' [ 비밀번호 초기화 설정 완료되었습니다.]',
            'DOMAIN'    => 'premium_c_1201550.lgdacom.net',
            'SESSIONID' => '7c87a95ea6e9f3ece019e21dcc0bed5c',
            'AUTHCODE'  => '196897',
        ];
    }

    public function testFromArraySuccess(): void
    {
        $result = PasswordResetResult::fromArray($this->successDatas());

        $this->assertStringContainsString('완료', $result->status);
        $this->assertSame('premium_c_1201550.lgdacom.net', $result->domain);
        $this->assertSame('7c87a95ea6e9f3ece019e21dcc0bed5c', $result->sessionId);
        $this->assertSame('196897', $result->authCode);
    }

    // 규격서 26.4 나.(2) 비밀번호 변경 실패 샘플
    public function testFromArrayFailure(): void
    {
        $result = PasswordResetResult::fromArray([
            'STATUS'    => '비밀번호 초기화 실패(이전 3회 비밀번호는 사용 할 수 없습니다)',
            'DOMAIN'    => 'premium_c_1201550.lgdacom.net',
            'SESSIONID' => '7c87a95ea6e9f3ece019e21dcc0bed5c',
            'AUTHCODE'  => '308887',
        ]);

        $this->assertStringContainsString('실패', $result->status);
        $this->assertSame('308887', $result->authCode);
    }

    public function testMissingFieldsDefaultToEmpty(): void
    {
        $result = PasswordResetResult::fromArray([]);

        $this->assertSame('', $result->status);
        $this->assertSame('', $result->domain);
        $this->assertSame('', $result->sessionId);
        $this->assertSame('', $result->authCode);
    }
}
