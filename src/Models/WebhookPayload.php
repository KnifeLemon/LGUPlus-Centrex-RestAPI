<?php

namespace LGUPlus\Centrex\Models;

/**
 * 웹훅 콜백 페이로드
 *
 * registerWebhook() 으로 등록한 URL 로 LGU+ 가 전달하는 수신 이벤트 데이터입니다.
 * $_GET 또는 파싱된 쿼리스트링 배열로부터 생성하세요.
 *
 * 호출 예시:
 *   event.php?sender=01012341234&receiver=070xxxx3002&kind=1&inner_num=302&message=
 */
class WebhookPayload
{
    /**
     * 이벤트 종류 상수
     */
    const KIND_CALL = '1'; // 전화
    const KIND_SMS  = '2'; // SMS

    /** 발신자 정보 (내선번호 또는 외부번호, 예: 01012341234, 300) */
    public string  $sender;

    /** 수신자 070 번호 (예: 070xxxx3002) */
    public string  $receiver;

    /**
     * 이벤트 종류
     *  '1' = 전화
     *  '2' = SMS
     */
    public string  $kind;

    /** 수신자 내선번호 (예: 302) */
    public string  $innerNum;

    /** 문자 수신 메시지 내용 (한글은 URLDECODE 처리됨) */
    public string  $message;

    public function __construct(
        string $sender,
        string $receiver,
        string $kind,
        string $innerNum,
        string $message
    ) {
        $this->sender   = $sender;
        $this->receiver = $receiver;
        $this->kind     = $kind;
        $this->innerNum = $innerNum;
        $this->message  = $message;
    }

    /**
     * $_GET 배열로부터 생성
     *
     * 웹훅 수신 페이지에서 바로 사용:
     *   $payload = WebhookPayload::fromGet($_GET);
     *
     * @param array<string, mixed> $get $_GET 배열
     */
    public static function fromGet(array $get): self
    {
        return new self(
            (string) ($get['sender']    ?? ''),
            (string) ($get['receiver']  ?? ''),
            (string) ($get['kind']      ?? ''),
            (string) ($get['inner_num'] ?? ''),
            urldecode((string) ($get['message'] ?? ''))
        );
    }

    /**
     * 쿼리스트링으로부터 생성
     *
     *   $payload = WebhookPayload::fromQueryString($queryString);
     */
    public static function fromQueryString(string $queryString): self
    {
        parse_str($queryString, $params);
        return self::fromGet($params);
    }

    /**
     * 전화 수신 이벤트인지 여부
     */
    public function isCall(): bool
    {
        return $this->kind === self::KIND_CALL;
    }

    /**
     * SMS 수신 이벤트인지 여부
     */
    public function isSms(): bool
    {
        return $this->kind === self::KIND_SMS;
    }

    /**
     * 배열 형태로 반환
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'sender'   => $this->sender,
            'receiver' => $this->receiver,
            'kind'     => $this->kind,
            'innerNum' => $this->innerNum,
            'message'  => $this->message,
        ];
    }
}
