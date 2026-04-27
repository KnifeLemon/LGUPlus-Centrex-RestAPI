<?php

namespace LGUPlus\Centrex\Tests\Models;

use LGUPlus\Centrex\Models\ErrorCode;
use PHPUnit\Framework\TestCase;

class ErrorCodeTest extends TestCase
{
    // 규격서 29. 에러코드 규격 정의 기반
    public function testConstantsMatchSpec(): void
    {
        $this->assertSame('0000', ErrorCode::OK);
        $this->assertSame('1001', ErrorCode::PARAM_MISSING);
        $this->assertSame('1002', ErrorCode::PARAM_INVALID);
        $this->assertSame('1003', ErrorCode::AUTH_INVALID);
        $this->assertSame('1004', ErrorCode::AUTH_WRONG_PASS);
        $this->assertSame('1005', ErrorCode::ERR_COMMAND);
        $this->assertSame('1007', ErrorCode::AUTH_INIT_PASS);
        $this->assertSame('1008', ErrorCode::AUTH_EXPIRED_PASS);
        $this->assertSame('1202', ErrorCode::OVER_CHANGE);
        $this->assertSame('1203', ErrorCode::AUTHCODE_USED);
        $this->assertSame('1204', ErrorCode::AUTHCODE_INVALID);
        $this->assertSame('2001', ErrorCode::NO_HOST);
        $this->assertSame('2002', ErrorCode::NO_PERM);
        $this->assertSame('3001', ErrorCode::ERR_PROCESS);
        $this->assertSame('3002', ErrorCode::OVER_MSG_LENGTH);
        $this->assertSame('3003', ErrorCode::OVER_DST_NUMBERS);
        $this->assertSame('3004', ErrorCode::NO_SMS_COUNT);
        $this->assertSame('4001', ErrorCode::UNKNOWN_COMMAND);
        $this->assertSame('4002', ErrorCode::NO_DATA);
        $this->assertSame('4003', ErrorCode::NO_STATUS);
        $this->assertSame('4004', ErrorCode::NO_CHANNEL);
        $this->assertSame('9999', ErrorCode::ETC_ERR);
    }

    public function testDescribeKnownCode(): void
    {
        $this->assertSame('정상처리', ErrorCode::describe('0000'));
        $this->assertSame('비밀번호 오류', ErrorCode::describe('1004'));
        $this->assertSame('인증코드 오류', ErrorCode::describe('1204'));
        $this->assertSame('기타 오류', ErrorCode::describe('9999'));
    }

    public function testDescribeUnknownCodeReturnsDefaultMessage(): void
    {
        $description = ErrorCode::describe('9998');
        $this->assertSame('알 수 없는 오류 코드', $description);
    }
}
