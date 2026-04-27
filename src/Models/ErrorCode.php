<?php

namespace LGUPlus\Centrex\Models;

/**
 * LGU+ Centrex REST API 오류 코드 정의
 */
class ErrorCode
{
    // --- 정상 ---
    const OK                = '0000'; // 정상처리

    // --- 파라미터 오류 ---
    const PARAM_MISSING     = '1001'; // 필수 Parameter 누락
    const PARAM_INVALID     = '1002'; // Parameter 정보 오류

    // --- 인증 오류 ---
    const AUTH_INVALID      = '1003'; // 회원정보 인증 오류
    const AUTH_WRONG_PASS   = '1004'; // 비밀번호 오류
    const AUTH_INIT_PASS    = '1007'; // 초기 비밀번호 사용 오류
    const AUTH_EXPIRED_PASS = '1008'; // 사용기간 만료 비밀번호 사용 오류

    // --- 명령 오류 ---
    const ERR_COMMAND       = '1005'; // 알 수 없는 명령

    // --- 인증코드 오류 ---
    const OVER_CHANGE       = '1202'; // 변경 시도수 초과 (20회/하루)
    const AUTHCODE_USED     = '1203'; // 이미 사용한 인증코드
    const AUTHCODE_INVALID  = '1204'; // 인증코드 오류

    // --- 호스트 / 권한 오류 ---
    const NO_HOST           = '2001'; // Host 정보 오류
    const NO_PERM           = '2002'; // 권한 없음

    // --- 처리 오류 ---
    const ERR_PROCESS       = '3001'; // 내부 프로세스 오류
    const OVER_MSG_LENGTH   = '3002'; // 메시지 길이 초과
    const OVER_DST_NUMBERS  = '3003'; // 동시발송 개수 초과
    const NO_SMS_COUNT      = '3004'; // SMS 남은 발송건수 없음

    // --- API 오류 ---
    const UNKNOWN_COMMAND   = '4001'; // API 명 오류
    const NO_DATA           = '4002'; // 정보 없음
    const NO_STATUS         = '4003'; // 알 수 없는 폰 상태
    const NO_CHANNEL        = '4004'; // 통화 중 채널 없음

    // --- 기타 ---
    const ETC_ERR           = '9999'; // 기타 오류

    /**
     * 오류 코드에 해당하는 설명 반환
     */
    public static function describe(string $code): string
    {
        $map = [
            self::OK                => '정상처리',
            self::PARAM_MISSING     => '필수 Parameter 누락',
            self::PARAM_INVALID     => 'Parameter 정보 오류',
            self::AUTH_INVALID      => '회원정보 인증 오류',
            self::AUTH_WRONG_PASS   => '비밀번호 오류',
            self::ERR_COMMAND       => '알 수 없는 명령',
            self::AUTH_INIT_PASS    => '초기 비밀번호 사용 오류',
            self::AUTH_EXPIRED_PASS => '사용기간 만료 비밀번호 사용 오류',
            self::OVER_CHANGE       => '변경 시도수 초과 (20회/하루)',
            self::AUTHCODE_USED     => '이미 사용한 인증코드',
            self::AUTHCODE_INVALID  => '인증코드 오류',
            self::NO_HOST           => 'Host 정보 오류',
            self::NO_PERM           => '권한 없음',
            self::ERR_PROCESS       => '내부 프로세스 오류',
            self::OVER_MSG_LENGTH   => '메시지 길이 초과',
            self::OVER_DST_NUMBERS  => '동시발송 개수 초과',
            self::NO_SMS_COUNT      => 'SMS 남은 발송건수 없음',
            self::UNKNOWN_COMMAND   => 'API 명 오류',
            self::NO_DATA           => '정보 없음',
            self::NO_STATUS         => '알 수 없는 폰 상태',
            self::NO_CHANNEL        => '통화 중 채널 없음',
            self::ETC_ERR           => '기타 오류',
        ];

        return $map[$code] ?? '알 수 없는 오류 코드';
    }
}
