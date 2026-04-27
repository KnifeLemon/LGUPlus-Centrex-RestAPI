<?php

namespace LGUPlus\Centrex\Tests\Models;

use LGUPlus\Centrex\Models\ReceivedSms;
use PHPUnit\Framework\TestCase;

class ReceivedSmsTest extends TestCase
{
    // 규격서 19.4 나.(1) 응답샘플 기반
    private function sampleItem(): array
    {
        return [
            'NO'       => 1,
            'TIME'     => '2019-08-30 11:04:54',
            'SRC'      => '010XXXX9087',
            'MNESSAGE' => '두번째 테스트 메세지입니다.',
        ];
    }

    public function testFromArray(): void
    {
        $sms = ReceivedSms::fromArray($this->sampleItem());

        $this->assertSame('1', $sms->no);
        $this->assertSame('2019-08-30 11:04:54', $sms->time);
        $this->assertSame('010XXXX9087', $sms->src);
        $this->assertSame('두번째 테스트 메세지입니다.', $sms->message);
    }

    /**
     * API 필드명 오타 'MNESSAGE'(MESSAGE 아님)를 정확히 매핑하는지 검증
     */
    public function testApiTypoFieldNameMappedCorrectly(): void
    {
        // MNESSAGE 키가 올 때 message 프로퍼티로 저장
        $sms = ReceivedSms::fromArray(['MNESSAGE' => '테스트']);
        $this->assertSame('테스트', $sms->message);

        // MESSAGE 키는 무시 (API 오타 그대로 처리)
        $sms2 = ReceivedSms::fromArray(['MESSAGE' => '테스트']);
        $this->assertSame('', $sms2->message);
    }

    public function testMissingFieldsDefaultToEmpty(): void
    {
        $sms = ReceivedSms::fromArray([]);

        $this->assertSame('', $sms->no);
        $this->assertSame('', $sms->time);
        $this->assertSame('', $sms->src);
        $this->assertSame('', $sms->message);
    }
}
