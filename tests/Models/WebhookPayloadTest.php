<?php

namespace LGUPlus\Centrex\Tests\Models;

use LGUPlus\Centrex\Models\WebhookPayload;
use PHPUnit\Framework\TestCase;

class WebhookPayloadTest extends TestCase
{
    // 규격서 13.6 나.(1) 외부번호 호출 예시 기반
    private function callGetParams(): array
    {
        return [
            'sender'    => '010xxxx5678',
            'receiver'  => '070xxxx3002',
            'kind'      => '1',
            'inner_num' => '302',
            'message'   => '',
        ];
    }

    // 규격서 16.6 가.(1) SMS URL 호출 예시 기반
    private function smsGetParams(): array
    {
        return [
            'sender'    => '300',
            'receiver'  => '070xxxx3002',
            'kind'      => '2',
            'inner_num' => '302',
            'message'   => 'ABC%EA%B0%80%EB%82%98%EB%8B%A4%21%40%23', // URLENCODE 상태
        ];
    }

    public function testFromGetCall(): void
    {
        $payload = WebhookPayload::fromGet($this->callGetParams());

        $this->assertSame('010xxxx5678', $payload->sender);
        $this->assertSame('070xxxx3002', $payload->receiver);
        $this->assertSame('1', $payload->kind);
        $this->assertSame('302', $payload->innerNum);
        $this->assertSame('', $payload->message);
    }

    public function testIsCallTrue(): void
    {
        $payload = WebhookPayload::fromGet($this->callGetParams());
        $this->assertTrue($payload->isCall());
        $this->assertFalse($payload->isSms());
    }

    public function testIsSmsTrue(): void
    {
        $payload = WebhookPayload::fromGet($this->smsGetParams());
        $this->assertTrue($payload->isSms());
        $this->assertFalse($payload->isCall());
    }

    public function testMessageIsUrldecoded(): void
    {
        $payload = WebhookPayload::fromGet(['message' => '%ED%99%8D%EA%B8%B8%EB%8F%99']);
        $this->assertSame('홍길동', $payload->message);
    }

    public function testFromQueryString(): void
    {
        $qs = 'sender=01012341234&receiver=070xxxx3002&kind=1&inner_num=302&message=';
        $payload = WebhookPayload::fromQueryString($qs);

        $this->assertSame('01012341234', $payload->sender);
        $this->assertSame('070xxxx3002', $payload->receiver);
        $this->assertSame('1', $payload->kind);
        $this->assertTrue($payload->isCall());
    }

    public function testFromQueryStringWithEncodedMessage(): void
    {
        $qs = 'sender=300&receiver=070xxxx3002&kind=2&inner_num=302&message=%ED%99%8D%EA%B8%B8%EB%8F%99';
        $payload = WebhookPayload::fromQueryString($qs);

        $this->assertTrue($payload->isSms());
        $this->assertSame('홍길동', $payload->message);
    }

    public function testKindConstants(): void
    {
        $this->assertSame('1', WebhookPayload::KIND_CALL);
        $this->assertSame('2', WebhookPayload::KIND_SMS);
    }

    public function testMissingFieldsDefaultToEmpty(): void
    {
        $payload = WebhookPayload::fromGet([]);

        $this->assertSame('', $payload->sender);
        $this->assertSame('', $payload->kind);
        $this->assertFalse($payload->isCall());
        $this->assertFalse($payload->isSms());
    }
}
