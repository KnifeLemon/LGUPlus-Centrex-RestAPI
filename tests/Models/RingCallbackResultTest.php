<?php

namespace LGUPlus\Centrex\Tests\Models;

use LGUPlus\Centrex\Models\RingCallbackResult;
use PHPUnit\Framework\TestCase;

class RingCallbackResultTest extends TestCase
{
    /**
     * 규격서 14.4 나. 응답샘플 기반 CALLBACK 문자열
     * 형식: 등록일^070번호^callbackhostip^callbackurl^port^종류^시작일^종료시
     */
    private function ringCallbackDatas(): array
    {
        return [
            'STATUS'   => 'OK',
            'DEBUG'    => '',
            'CALLBACK' => '2014-09-04 18:35:23^070xxxx3002^61.xx.xx.71^/www/ringa_302.html^80^1^20140904183523^29991212240',
        ];
    }

    private function smsCallbackDatas(): array
    {
        return [
            'STATUS'   => 'OK',
            'DEBUG'    => '',
            'CALLBACK' => '2014-09-04 20:00:07^070xxxx3002^61.xx.xx.71^/www/smsaa_302.html^80^2^20140904200007^299912122400',
        ];
    }

    public function testFromArrayParsesCallbackString(): void
    {
        $result = RingCallbackResult::fromArray($this->ringCallbackDatas());

        $this->assertSame('OK', $result->status);
        $this->assertSame('2014-09-04 18:35:23', $result->registeredAt);
        $this->assertSame('070xxxx3002', $result->number070);
        $this->assertSame('61.xx.xx.71', $result->callbackHost);
        $this->assertSame('/www/ringa_302.html', $result->callbackUrl);
        $this->assertSame('80', $result->callbackPort);
        $this->assertSame('1', $result->kind);
        $this->assertSame('20140904183523', $result->startAt);
    }

    public function testIsRingTrue(): void
    {
        $result = RingCallbackResult::fromArray($this->ringCallbackDatas());
        $this->assertTrue($result->isRing());
        $this->assertFalse($result->isSms());
    }

    public function testIsSmsTrue(): void
    {
        $result = RingCallbackResult::fromArray($this->smsCallbackDatas());
        $this->assertTrue($result->isSms());
        $this->assertFalse($result->isRing());
    }

    public function testKindConstants(): void
    {
        $this->assertSame('1', RingCallbackResult::KIND_RING);
        $this->assertSame('2', RingCallbackResult::KIND_SMS);
    }

    public function testMissingCallbackStringYieldsEmptyFields(): void
    {
        $result = RingCallbackResult::fromArray(['STATUS' => 'OK']);

        $this->assertSame('OK', $result->status);
        $this->assertSame('', $result->registeredAt);
        $this->assertSame('', $result->number070);
        $this->assertSame('', $result->callbackHost);
    }
}
