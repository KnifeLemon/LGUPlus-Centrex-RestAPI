<?php

namespace LGUPlus\Centrex\Tests\Models;

use LGUPlus\Centrex\Models\ForwardTypeInfo;
use PHPUnit\Framework\TestCase;

class ForwardTypeInfoTest extends TestCase
{
    // 규격서 21.4 나. 응답샘플 기반
    private function forwardActiveDatas(): array
    {
        return [
            'FORWARD_USE'  => 'F',
            'FORWARD_DATA' => '07011112222',
            'FORWARD_STR'  => '매일외부:무조건:07011112222',
            'FORWARD_TYPE' => '0',
            'DOMAIN'       => 'lgdacom.net',
        ];
    }

    public function testFromArray(): void
    {
        $info = ForwardTypeInfo::fromArray($this->forwardActiveDatas());

        $this->assertSame('F', $info->forwardUse);
        $this->assertSame('07011112222', $info->forwardData);
        $this->assertSame('매일외부:무조건:07011112222', $info->forwardStr);
        $this->assertSame('0', $info->forwardType);
        $this->assertSame('lgdacom.net', $info->domain);
    }

    public function testIsActiveWhenForwardTypeF(): void
    {
        $info = ForwardTypeInfo::fromArray(['FORWARD_USE' => 'F']);
        $this->assertTrue($info->isActive());
    }

    public function testIsActiveWhenVoicemail(): void
    {
        $info = ForwardTypeInfo::fromArray(['FORWARD_USE' => 'V']);
        $this->assertTrue($info->isActive());
    }

    public function testIsNotActiveWhenNone(): void
    {
        $info = ForwardTypeInfo::fromArray(['FORWARD_USE' => 'N']);
        $this->assertFalse($info->isActive());
    }

    public function testIsNotActiveWhenEmpty(): void
    {
        $info = ForwardTypeInfo::fromArray(['FORWARD_USE' => '']);
        $this->assertFalse($info->isActive());
    }

    public function testConstants(): void
    {
        $this->assertSame('F', ForwardTypeInfo::USE_FORWARD);
        $this->assertSame('V', ForwardTypeInfo::USE_VOICEMAIL);
        $this->assertSame('N', ForwardTypeInfo::USE_NONE);
    }

    public function testMissingFieldsDefaultToEmpty(): void
    {
        $info = ForwardTypeInfo::fromArray([]);

        $this->assertSame('', $info->forwardUse);
        $this->assertSame('', $info->forwardData);
        $this->assertSame('', $info->domain);
    }
}
