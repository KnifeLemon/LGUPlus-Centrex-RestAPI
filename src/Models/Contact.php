<?php

namespace LGUPlus\Centrex\Models;

/**
 * 주소록 연락처
 *
 * addressList() / getAllAddresses() 응답의 DATAS 항목 하나를 나타냅니다.
 */
class Contact
{
    /** 번호 (고유 식별자) */
    public string  $no;

    /** 이름 */
    public string  $name;

    /** 전화번호 1 */
    public string  $phone1;

    /** 전화번호 2 */
    public ?string $phone2;

    /** 그룹명 */
    public ?string $groupName;

    /** 그룹코드 */
    public ?string $groupCode;

    /** 비고 */
    public ?string $etc;

    public function __construct(
        string  $no,
        string  $name,
        string  $phone1,
        ?string $phone2    = null,
        ?string $groupName = null,
        ?string $groupCode = null,
        ?string $etc       = null
    ) {
        $this->no        = $no;
        $this->name      = $name;
        $this->phone1    = $phone1;
        $this->phone2    = $phone2;
        $this->groupName = $groupName;
        $this->groupCode = $groupCode;
        $this->etc       = $etc;
    }

    /**
     * API 응답 배열 한 항목으로부터 생성
     *
     * @param array<string, mixed> $data DATAS 배열의 단일 항목
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['NO']     ?? ''),
            (string) ($data['NAME']   ?? ''),
            (string) ($data['PHONE1'] ?? ''),
            isset($data['PHONE2'])     && $data['PHONE2']     !== '' ? (string) $data['PHONE2']     : null,
            isset($data['GROUP_NAME']) && $data['GROUP_NAME'] !== '' ? (string) $data['GROUP_NAME'] : null,
            isset($data['GROUP_CODE']) && $data['GROUP_CODE'] !== '' ? (string) $data['GROUP_CODE'] : null,
            isset($data['ETC'])        && $data['ETC']        !== '' ? (string) $data['ETC']        : null
        );
    }

    /**
     * 배열 형태로 반환
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'no'        => $this->no,
            'name'      => $this->name,
            'phone1'    => $this->phone1,
            'phone2'    => $this->phone2,
            'groupName' => $this->groupName,
            'groupCode' => $this->groupCode,
            'etc'       => $this->etc,
        ], fn($v) => $v !== null);
    }
}
