<?php

namespace LGUPlus\Centrex\Models;

/**
 * 패스워드 초기화 요청 결과 (setauthchange)
 */
class PasswordResetResult
{
    /** 수행 결과 안내 문구 */
    public string $status;

    /** 사용자 도메인 정보 */
    public string $domain;

    /** Session-ID (5분간 유효) */
    public string $sessionId;

    /** 단말에 수신된 6자리 인증코드 */
    public string $authCode;

    public function __construct(
        string $status,
        string $domain,
        string $sessionId,
        string $authCode
    ) {
        $this->status    = $status;
        $this->domain    = $domain;
        $this->sessionId = $sessionId;
        $this->authCode  = $authCode;
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
            (string) ($data['DOMAIN']    ?? ''),
            (string) ($data['SESSIONID'] ?? ''),
            (string) ($data['AUTHCODE']  ?? '')
        );
    }
}
