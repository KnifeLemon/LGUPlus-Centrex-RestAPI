<?php

namespace LGUPlus\Centrex\Models;

/**
 * 전화 수신 URL 알림 설정 조회 결과
 *
 * getRingCallback() 응답의 DATAS 를 나타냅니다.
 *
 * CALLBACK 필드 형식 ('^' 구분):
 *   등록일^070번호(ID)^callbackhostip^callbackurl^port^종류^시작일^종료시
 */
class RingCallbackResult
{
    /**
     * 콜백 종류 상수
     */
    const KIND_RING = '1'; // 전화 수신 알림
    const KIND_SMS  = '2'; // SMS 수신 알림

    /** 명령수행 결과 메시지 */
    public string  $status;

    /** 결과값 정보 */
    public string  $debug;

    /** 등록일 */
    public string  $registeredAt;

    /** 070 번호 (ID) */
    public string  $number070;

    /** 콜백 호출 대상 서버 IP */
    public string  $callbackHost;

    /** 콜백 호출 URL */
    public string  $callbackUrl;

    /** 콜백 호출 포트 */
    public string  $callbackPort;

    /**
     * 콜백 종류
     *  '1' = 전화 수신 알림 (ring)
     *  '2' = SMS 수신 알림
     */
    public string  $kind;

    /** 시작일 */
    public string  $startAt;

    /** 종료시 */
    public string  $endAt;

    public function __construct(
        string $status,
        string $debug,
        string $registeredAt,
        string $number070,
        string $callbackHost,
        string $callbackUrl,
        string $callbackPort,
        string $kind,
        string $startAt,
        string $endAt
    ) {
        $this->status       = $status;
        $this->debug        = $debug;
        $this->registeredAt = $registeredAt;
        $this->number070    = $number070;
        $this->callbackHost = $callbackHost;
        $this->callbackUrl  = $callbackUrl;
        $this->callbackPort = $callbackPort;
        $this->kind         = $kind;
        $this->startAt      = $startAt;
        $this->endAt        = $endAt;
    }

    /**
     * API 응답 배열로부터 생성
     *
     * @param array<string, mixed> $data DATAS 배열
     */
    public static function fromArray(array $data): self
    {
        $parts = explode('^', (string) ($data['CALLBACK'] ?? ''));

        return new self(
            (string) ($data['STATUS'] ?? ''),
            (string) ($data['DEBUG']  ?? ''),
            $parts[0] ?? '', // 등록일
            $parts[1] ?? '', // 070번호(ID)
            $parts[2] ?? '', // callbackhostip
            $parts[3] ?? '', // callbackurl
            $parts[4] ?? '', // port
            $parts[5] ?? '', // 종류 (1=ring, 2=SMS)
            $parts[6] ?? '', // 시작일
            $parts[7] ?? ''  // 종료시
        );
    }

    /**
     * 전화 수신 알림인지 여부
     */
    public function isRing(): bool
    {
        return $this->kind === self::KIND_RING;
    }

    /**
     * SMS 수신 알림인지 여부
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
            'status'       => $this->status,
            'debug'        => $this->debug,
            'registeredAt' => $this->registeredAt,
            'number070'    => $this->number070,
            'callbackHost' => $this->callbackHost,
            'callbackUrl'  => $this->callbackUrl,
            'callbackPort' => $this->callbackPort,
            'kind'         => $this->kind,
            'startAt'      => $this->startAt,
            'endAt'        => $this->endAt,
        ];
    }
}
