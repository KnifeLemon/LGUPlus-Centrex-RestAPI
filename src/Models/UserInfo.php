<?php

namespace LGUPlus\Centrex\Models;

/**
 * 사용자 정보
 *
 * userInfo() 응답의 DATAS 를 나타냅니다.
 */
class UserInfo
{
    /**
     * 착신전환 타입 상수
     */
    const FORWARD_TYPE_FORWARD   = 'F'; // 착신전환전화
    const FORWARD_TYPE_VOICEMAIL = 'V'; // 음성사서함
    const FORWARD_TYPE_NONE      = 'N'; // 사용안함

    /** 사용자 이름 */
    public string  $name;

    /** 내선번호 */
    public string  $exten;

    /** 070 번호 */
    public string  $number070;

    /**
     * 착신전화 설정 형식
     *  'F' = 착신전환전화
     *  'V' = 음성사서함
     *  'N' = 사용안함
     */
    public string  $forwardType;

    /** 착신전환 설정값 */
    public string  $forwardData;

    /** 착신전환 설명 */
    public string  $forwardStr;

    public function __construct(
        string $name,
        string $exten,
        string $number070,
        string $forwardType,
        string $forwardData,
        string $forwardStr
    ) {
        $this->name        = $name;
        $this->exten       = $exten;
        $this->number070   = $number070;
        $this->forwardType = $forwardType;
        $this->forwardData = $forwardData;
        $this->forwardStr  = $forwardStr;
    }

    /**
     * API 응답 배열로부터 생성
     *
     * @param array<string, mixed> $data DATAS 배열
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['NAME']         ?? ''),
            (string) ($data['EXTEN']        ?? ''),
            (string) ($data['NUMBER070']    ?? ''),
            (string) ($data['FORWARD_TYPE'] ?? ''),
            (string) ($data['FORWARD_DATA'] ?? ''),
            (string) ($data['FORWARD_STR']  ?? '')
        );
    }

    /**
     * 착신전환이 활성화 되어있는지 여부
     */
    public function isForwardActive(): bool
    {
        return $this->forwardType !== self::FORWARD_TYPE_NONE
            && $this->forwardType !== '';
    }

    /**
     * 배열 형태로 반환
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name'        => $this->name,
            'exten'       => $this->exten,
            'number070'   => $this->number070,
            'forwardType' => $this->forwardType,
            'forwardData' => $this->forwardData,
            'forwardStr'  => $this->forwardStr,
        ];
    }
}
