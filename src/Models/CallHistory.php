<?php

namespace LGUPlus\Centrex\Models;

/**
 * 통화 이력
 *
 * callHistory() 응답의 DATAS 항목 하나를 나타냅니다.
 */
class CallHistory
{
    /** 번호 (고유 식별자) */
    public string  $no;

    /** 시작시간 */
    public string  $time;

    /** 발신자번호 */
    public string  $src;

    /** 수신자번호 (외부번호는 보안상 7자리만 표시) */
    public string  $dst;

    /** 총발신시간 - 벨시간 포함 (초) */
    public string  $duration;

    /** 실제통화시간 (초) */
    public string  $billSec;

    /** 통화상태 */
    public string  $status;

    /**
     * 통화구분
     *  0 = 내부
     *  1 = 시내/시외
     *  2 = 이동통신
     *  3 = 국제전화
     */
    public string  $kind;

    public function __construct(
        string $no,
        string $time,
        string $src,
        string $dst,
        string $duration,
        string $billSec,
        string $status,
        string $kind
    ) {
        $this->no       = $no;
        $this->time     = $time;
        $this->src      = $src;
        $this->dst      = $dst;
        $this->duration = $duration;
        $this->billSec  = $billSec;
        $this->status   = $status;
        $this->kind     = $kind;
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
            (string) ($data['DST']      ?? ''),
            (string) ($data['DURATION'] ?? ''),
            (string) ($data['BILLSEC']  ?? ''),
            (string) ($data['STATUS']   ?? ''),
            (string) ($data['KIND']     ?? '')
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
            'no'       => $this->no,
            'time'     => $this->time,
            'src'      => $this->src,
            'dst'      => $this->dst,
            'duration' => $this->duration,
            'billSec'  => $this->billSec,
            'status'   => $this->status,
            'kind'     => $this->kind,
        ];
    }
}
