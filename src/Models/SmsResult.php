<?php

namespace LGUPlus\Centrex\Models;

/**
 * SMS 발송 결과
 *
 * smsSend() 응답의 DATAS 를 나타냅니다.
 */
class SmsResult
{
    /** 명령수행 결과 메시지 ("OK" | "UNKNOW" | ...) */
    public string $status;

    /** 발신 성공 정보 ("01012341234=OK,016...") */
    public string $debug;

    /** 남은 발송 가능 개수 */
    public int $restCount;

    public function __construct(string $status, string $debug, int $restCount)
    {
        $this->status    = $status;
        $this->debug     = $debug;
        $this->restCount = $restCount;
    }

    /**
     * API 응답 배열로부터 생성
     *
     * @param array<string, mixed> $data DATAS 배열
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['STATUS']    ?? ''),
            (string) ($data['DEBUG']     ?? ''),
            (int)    ($data['RESTCOUNT'] ?? 0)
        );
    }

    /**
     * 발송이 성공했는지 여부
     */
    public function isOk(): bool
    {
        return $this->status === 'OK';
    }

    /**
     * 배열 형태로 반환
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'status'    => $this->status,
            'debug'     => $this->debug,
            'restCount' => $this->restCount,
        ];
    }
}
