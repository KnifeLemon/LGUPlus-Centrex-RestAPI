<?php

namespace LGUPlus\Centrex\Models;

/**
 * 수신 문자 메시지
 *
 * getRecvSmsList() 응답의 DATAS 항목 하나를 나타냅니다.
 */
class ReceivedSms
{
    /** 순서 정보 */
    public string $no;

    /** 수신 시간 */
    public string $time;

    /** 발신 번호 */
    public string $src;

    /** 메시지 내용 */
    public string $message;

    public function __construct(
        string $no,
        string $time,
        string $src,
        string $message
    ) {
        $this->no      = $no;
        $this->time    = $time;
        $this->src     = $src;
        $this->message = $message;
    }

    /**
     * API 응답 배열 한 항목으로부터 생성
     *
     * @param array<string, mixed> $data DATAS 배열의 단일 항목
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['NO']       ?? ''),
            (string) ($data['TIME']     ?? ''),
            (string) ($data['SRC']      ?? ''),
            (string) ($data['MNESSAGE'] ?? '') // API 원문 오타 그대로 사용
        );
    }

    /**
     * 배열 형태로 반환
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'no'      => $this->no,
            'time'    => $this->time,
            'src'     => $this->src,
            'message' => $this->message,
        ];
    }
}
