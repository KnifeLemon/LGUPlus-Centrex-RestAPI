<?php

namespace LGUPlus\Centrex\Models;

/**
 * 패스워드 초기화 인증코드 요청 결과 (getauthcode)
 */
class AuthCodeResult
{
    /** 인증코드 전송 안내 문구 */
    public string $status;

    /**
     * 웹에서 생성한 Session-ID
     * 비밀번호 변경 요청 시 함께 전달해야 하며 5분간 유효합니다.
     */
    public string $sessionId;

    /** 인증 시도 횟수 */
    public int $tryCnt;

    public function __construct(string $status, string $sessionId, int $tryCnt)
    {
        $this->status    = $status;
        $this->sessionId = $sessionId;
        $this->tryCnt    = $tryCnt;
    }

    /**
     * API 응답 DATAS 배열로부터 생성
     *
     * @param array<string, mixed> $data DATAS 값
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['STATUS']    ?? ''),
            (string) ($data['SESSIONID'] ?? ''),
            (int)    ($data['TRYCNT']    ?? 0)
        );
    }
}
