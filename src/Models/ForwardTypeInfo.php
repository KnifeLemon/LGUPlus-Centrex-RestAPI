<?php

namespace LGUPlus\Centrex\Models;

/**
 * 착신전환 유형별 설정 정보
 *
 * getForwardType() 응답의 DATAS 를 나타냅니다.
 */
class ForwardTypeInfo
{
    /**
     * 착신전환 설정 상태 상수
     */
    const USE_FORWARD    = 'F'; // 착신전환
    const USE_VOICEMAIL  = 'V'; // 음성메일
    const USE_NONE       = 'N'; // 해제

    /**
     * 착신전환 설정 상태
     *  'F' = 착신전환, 'V' = 음성메일, 'N' | '' = 해제
     */
    public string  $forwardUse;

    /** 착신전환 번호 (예: 07011112222) */
    public string  $forwardData;

    /** 착신전환 설명 (예: 매일외부:통화중일때:07011112222) */
    public string  $forwardStr;

    /**
     * 착신전환 유형
     *  '0' = 무조건
     *  '1' = 받지 않을 경우
     *  '2' = 통화중일 경우
     *  '3' = 동시연결
     *  '4' = 통화중 또는 무응답시
     *  ''  = 음성메일 설정 또는 해제
     */
    public string  $forwardType;

    /** 도메인명 */
    public string  $domain;

    public function __construct(
        string $forwardUse,
        string $forwardData,
        string $forwardStr,
        string $forwardType,
        string $domain
    ) {
        $this->forwardUse  = $forwardUse;
        $this->forwardData = $forwardData;
        $this->forwardStr  = $forwardStr;
        $this->forwardType = $forwardType;
        $this->domain      = $domain;
    }

    /**
     * API 응답 배열로부터 생성
     *
     * @param array<string, mixed> $data DATAS 배열
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['FORWARD_USE']  ?? ''),
            (string) ($data['FORWARD_DATA'] ?? ''),
            (string) ($data['FORWARD_STR']  ?? ''),
            (string) ($data['FORWARD_TYPE'] ?? ''),
            (string) ($data['DOMAIN']       ?? '')
        );
    }

    /**
     * 착신전환이 활성화 되어 있는지 여부
     */
    public function isActive(): bool
    {
        return $this->forwardUse === self::USE_FORWARD
            || $this->forwardUse === self::USE_VOICEMAIL;
    }

    /**
     * 배열 형태로 반환
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'forwardUse'  => $this->forwardUse,
            'forwardData' => $this->forwardData,
            'forwardStr'  => $this->forwardStr,
            'forwardType' => $this->forwardType,
            'domain'      => $this->domain,
        ];
    }
}
