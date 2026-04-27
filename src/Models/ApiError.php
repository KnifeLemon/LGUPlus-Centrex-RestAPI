<?php

namespace LGUPlus\Centrex\Models;

/**
 * API 오류 응답
 *
 * isSuccess() 가 false 일 때 getApiError() 로 반환됩니다.
 */
class ApiError
{
    /** API 오류 코드 (SVC_RT) */
    public string $code;

    /** API 오류 메시지 (SVC_MSG) */
    public string $message;

    public function __construct(string $code, string $message)
    {
        $this->code    = $code;
        $this->message = $message;
    }

    /**
     * API 응답 배열로부터 생성
     *
     * @param array<string, mixed> $response 전체 응답 배열
     */
    public static function fromResponse(array $response): self
    {
        return new self(
            (string) ($response['SVC_RT']  ?? ''),
            (string) ($response['SVC_MSG'] ?? '')
        );
    }

    public function __toString(): string
    {
        return "[{$this->code}] {$this->message}";
    }
}
