<?php

namespace LGUPlus\Centrex\Tests\Models;

use LGUPlus\Centrex\Models\InboundCall;
use PHPUnit\Framework\TestCase;

class InboundCallTest extends TestCase
{
    // 규격서 22.6 나.(1) 응답샘플 첫 번째 항목 기반
    private function answeredItem(): array
    {
        return [
            'NO'         => 1,
            'TIME'       => '2019-08-13 13:53:54',
            'SRC'        => '010XXXX9087',
            'DST'        => '10230852',
            'DURATION'   => '2',
            'STATUS'     => 'ANSWERED',
            'CHANNEL'    => 'SIP/5060-b723dff8a7df',
            'DSTCHANNEL' => 'SIP/10230852-6ffe',
            'ENDTIME'    => '2019-08-13 13:53:56',
            'APPDATA'    => 'SIP/10230852',
        ];
    }

    // 규격서 22.6 나.(1) 취소 항목
    private function cancelItem(): array
    {
        return [
            'NO'         => 6,
            'TIME'       => '2019-08-09 17:48:11',
            'SRC'        => '010XXXX9087',
            'DST'        => '07075992854',
            'DURATION'   => '6',
            'STATUS'     => 'CANCEL',
            'CHANNEL'    => 'SIP/5060-b723535034f7',
            'DSTCHANNEL' => 'SIP/10230854-1218',
            'ENDTIME'    => '2019-08-09 17:48:17',
            'APPDATA'    => 'SIP/10230854',
        ];
    }

    public function testFromArrayAnswered(): void
    {
        $call = InboundCall::fromArray($this->answeredItem());

        $this->assertSame('1', $call->no);
        $this->assertSame('2019-08-13 13:53:54', $call->time);
        $this->assertSame('010XXXX9087', $call->src);
        $this->assertSame('10230852', $call->dst);
        $this->assertSame('2', $call->duration);
        $this->assertSame('ANSWERED', $call->status);
        $this->assertSame('SIP/5060-b723dff8a7df', $call->channel);
        $this->assertSame('SIP/10230852-6ffe', $call->dstChannel);
        $this->assertSame('2019-08-13 13:53:56', $call->endTime);
        $this->assertSame('SIP/10230852', $call->appData);
    }

    public function testIsAnsweredTrue(): void
    {
        $call = InboundCall::fromArray($this->answeredItem());
        $this->assertTrue($call->isAnswered());
    }

    public function testIsAnsweredFalseForCancel(): void
    {
        $call = InboundCall::fromArray($this->cancelItem());
        $this->assertFalse($call->isAnswered());
    }

    public function testStatusConstants(): void
    {
        $this->assertSame('ANSWERED',  InboundCall::STATUS_ANSWERED);
        $this->assertSame('NO ANSWER', InboundCall::STATUS_NO_ANSWER);
        $this->assertSame('CANCEL',    InboundCall::STATUS_CANCEL);
        $this->assertSame('BUSY',      InboundCall::STATUS_BUSY);
        $this->assertSame('FAILED',    InboundCall::STATUS_FAILED);
    }

    public function testToArray(): void
    {
        $call = InboundCall::fromArray($this->answeredItem());
        $arr  = $call->toArray();

        $this->assertSame('1', $arr['no']);
        $this->assertSame('ANSWERED', $arr['status']);
        $this->assertArrayHasKey('dstChannel', $arr);
        $this->assertArrayHasKey('endTime', $arr);
        $this->assertArrayHasKey('appData', $arr);
    }

    public function testMissingFieldsDefaultToEmpty(): void
    {
        $call = InboundCall::fromArray([]);
        $this->assertSame('', $call->no);
        $this->assertSame('', $call->status);
        $this->assertFalse($call->isAnswered());
    }
}
