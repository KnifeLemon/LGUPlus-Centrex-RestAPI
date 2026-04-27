<?php

namespace LGUPlus\Centrex\Models;

/**
 * 통화 채널 상태
 *
 * channelStatus() 응답의 DATAS 를 나타냅니다.
 */
class ChannelStatus
{
    /** 통화상태인 채널 정보 */
    public string $status;

    public function __construct(string $status)
    {
        $this->status = $status;
    }

    /**
     * API 응답 배열로부터 생성
     *
     * @param array<string, mixed> $data DATAS 배열
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['STATUS'] ?? '')
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
            'status' => $this->status,
        ];
    }
}
