<?php

namespace LGUPlus\Centrex\Tests\Models;

use LGUPlus\Centrex\Models\UserInfo;
use PHPUnit\Framework\TestCase;

class UserInfoTest extends TestCase
{
    // 규격서 11.3 라. 구조 기반
    private function forwardActiveDatas(): array
    {
        return [
            'NAME'         => '홍길동',
            'EXTEN'        => '3002',
            'NUMBER070'    => '07075993002',
            'FORWARD_TYPE' => 'F',
            'FORWARD_DATA' => '07011112222',
            'FORWARD_STR'  => '착신전환 설정 중',
        ];
    }

    public function testFromArray(): void
    {
        $info = UserInfo::fromArray($this->forwardActiveDatas());

        $this->assertSame('홍길동', $info->name);
        $this->assertSame('3002', $info->exten);
        $this->assertSame('07075993002', $info->number070);
        $this->assertSame('F', $info->forwardType);
        $this->assertSame('07011112222', $info->forwardData);
        $this->assertSame('착신전환 설정 중', $info->forwardStr);
    }

    public function testIsForwardActiveWhenF(): void
    {
        $info = UserInfo::fromArray(['FORWARD_TYPE' => 'F']);
        $this->assertTrue($info->isForwardActive());
    }

    public function testIsForwardActiveWhenVoicemail(): void
    {
        $info = UserInfo::fromArray(['FORWARD_TYPE' => 'V']);
        $this->assertTrue($info->isForwardActive());
    }

    public function testIsForwardNotActiveWhenN(): void
    {
        $info = UserInfo::fromArray(['FORWARD_TYPE' => 'N']);
        $this->assertFalse($info->isForwardActive());
    }

    public function testConstants(): void
    {
        $this->assertSame('F', UserInfo::FORWARD_TYPE_FORWARD);
        $this->assertSame('V', UserInfo::FORWARD_TYPE_VOICEMAIL);
        $this->assertSame('N', UserInfo::FORWARD_TYPE_NONE);
    }

    public function testMissingFieldsDefaultToEmpty(): void
    {
        $info = UserInfo::fromArray([]);

        $this->assertSame('', $info->name);
        $this->assertSame('', $info->forwardType);
        $this->assertFalse($info->isForwardActive());
    }
}
