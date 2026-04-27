<?php

namespace LGUPlus\Centrex\Models;

/**
 * 외부인입번호별 수신 통화 이력
 *
 * getInboundCall() 응답의 DATAS 항목 하나를 나타냅니다.
 */
class InboundCall
{
    /**
     * 통화 결과 상태 상수
     */
    const STATUS_ANSWERED  = 'ANSWERED';   // 통화 성공
    const STATUS_NO_ANSWER = 'NO ANSWER';  // 무응답
    const STATUS_CANCEL    = 'CANCEL';     // 발신 측이 통화 연결 전 종료
    const STATUS_BUSY      = 'BUSY';       // 통화 중
    const STATUS_FAILED    = 'FAILED';     // 기타 통화 실패

    /** 일련번호 */
    public string $no;

    /** 통화 시작 시간 */
    public string $time;

    /** 발신 전화번호 */
    public string $src;

    /** 수신 대상 참고용 번호 정보 (인입된 070번호) */
    public string $dst;

    /** 시작시간과 종료시간에 걸린 시간 */
    public string $duration;

    /**
     * 통화 결과 상태
     *  'ANSWERED'  = 통화 성공
     *  'NO ANSWER' = 무응답
     *  'CANCEL'    = 발신 측이 통화 연결 전 종료
     *  'BUSY'      = 통화 중
     *  'FAILED'    = 기타 통화 실패
     */
    public string $status;

    /** 발신 채널 정보 */
    public string $channel;

    /** 수신 대상 참고용 채널 정보 (외부 착신전환 시 외부 IP는 마스킹) */
    public string $dstChannel;

    /** 통화 종료 시간 */
    public string $endTime;

    /** 수신 대상 참고용 연결 정보 */
    public string $appData;

    public function __construct(
        string $no,
        string $time,
        string $src,
        string $dst,
        string $duration,
        string $status,
        string $channel,
        string $dstChannel,
        string $endTime,
        string $appData
    ) {
        $this->no         = $no;
        $this->time       = $time;
        $this->src        = $src;
        $this->dst        = $dst;
        $this->duration   = $duration;
        $this->status     = $status;
        $this->channel    = $channel;
        $this->dstChannel = $dstChannel;
        $this->endTime    = $endTime;
        $this->appData    = $appData;
    }

    /**
     * API 응답 배열 한 항목으로부터 생성
     *
     * @param array<string, mixed> $data DATAS 배열의 단일 항목
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['NO']         ?? ''),
            (string) ($data['TIME']       ?? ''),
            (string) ($data['SRC']        ?? ''),
            (string) ($data['DST']        ?? ''),
            (string) ($data['DURATION']   ?? ''),
            (string) ($data['STATUS']     ?? ''),
            (string) ($data['CHANNEL']    ?? ''),
            (string) ($data['DSTCHANNEL'] ?? ''),
            (string) ($data['ENDTIME']    ?? ''),
            (string) ($data['APPDATA']    ?? '')
        );
    }

    /**
     * 통화가 성공했는지 여부
     */
    public function isAnswered(): bool
    {
        return $this->status === self::STATUS_ANSWERED;
    }

    /**
     * 배열 형태로 반환
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'no'         => $this->no,
            'time'       => $this->time,
            'src'        => $this->src,
            'dst'        => $this->dst,
            'duration'   => $this->duration,
            'status'     => $this->status,
            'channel'    => $this->channel,
            'dstChannel' => $this->dstChannel,
            'endTime'    => $this->endTime,
            'appData'    => $this->appData,
        ];
    }
}
