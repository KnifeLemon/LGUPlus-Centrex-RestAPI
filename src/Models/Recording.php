<?php

namespace LGUPlus\Centrex\Models;

/**
 * 녹취 파일 정보
 *
 * recordList() 응답의 DATAS 항목 하나를 나타냅니다.
 */
class Recording
{
    /** 번호 (고유 식별자) */
    public string  $no;

    /** 파일명 */
    public string  $fileName;

    /** 발신자번호 */
    public string  $src;

    /** 수신자번호 */
    public string  $dst;

    /** 통화시간 */
    public string  $callDate;

    /** 파일생성시간 */
    public string  $fileTime;

    /** 파일용량 */
    public string  $fileSize;

    public function __construct(
        string $no,
        string $fileName,
        string $src,
        string $dst,
        string $callDate,
        string $fileTime,
        string $fileSize
    ) {
        $this->no       = $no;
        $this->fileName = $fileName;
        $this->src      = $src;
        $this->dst      = $dst;
        $this->callDate = $callDate;
        $this->fileTime = $fileTime;
        $this->fileSize = $fileSize;
    }

    /**
     * API 응답 배열 한 항목으로부터 생성
     *
     * @param array<string, mixed> $data DATAS 배열의 단일 항목
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['NO']        ?? ''),
            (string) ($data['FILE_NAME'] ?? ''),
            (string) ($data['SRC']       ?? ''),
            (string) ($data['DST']       ?? ''),
            (string) ($data['CALLDATE']  ?? ''),
            (string) ($data['FILE_TIME'] ?? ''),
            (string) ($data['FILE_SIZE'] ?? '')
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
            'fileName' => $this->fileName,
            'src'      => $this->src,
            'dst'      => $this->dst,
            'callDate' => $this->callDate,
            'fileTime' => $this->fileTime,
            'fileSize' => $this->fileSize,
        ];
    }
}
