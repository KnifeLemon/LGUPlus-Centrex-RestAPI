<?php

namespace LGUPlus\Centrex;

use LGUPlus\Centrex\Models\ApiError;
use LGUPlus\Centrex\Models\AuthCodeResult;
use LGUPlus\Centrex\Models\CallHistory;
use LGUPlus\Centrex\Models\ChannelStatus;
use LGUPlus\Centrex\Models\Contact;
use LGUPlus\Centrex\Models\ForwardTypeInfo;
use LGUPlus\Centrex\Models\InboundCall;
use LGUPlus\Centrex\Models\ListInfo;
use LGUPlus\Centrex\Models\PasswordResetResult;
use LGUPlus\Centrex\Models\ReceivedSms;
use LGUPlus\Centrex\Models\Recording;
use LGUPlus\Centrex\Models\RingCallbackResult;
use LGUPlus\Centrex\Models\SmsResult;
use LGUPlus\Centrex\Models\UserInfo;

class LGUPlus
{
    private const API_URL = 'https://centrex.uplus.co.kr/RestApi';
    private const TIMEOUT = 10;

    /** 착신전환 유형: 무조건 */
    const FORWARD_TYPE_ALWAYS        = '0';
    /** 착신전환 유형: 받지 않을 경우 */
    const FORWARD_TYPE_NO_ANSWER     = '1';
    /** 착신전환 유형: 통화중일 경우 */
    const FORWARD_TYPE_BUSY          = '2';
    /** 착신전환 유형: 동시연결 */
    const FORWARD_TYPE_SIMULTANEOUS  = '3';
    /** 착신전환 유형: 통화중 또는 무응답시 */
    const FORWARD_TYPE_BUSY_OR_NO_ANSWER = '4';

    private string $id;
    private string $password;
    private ?array $lastResponse = null;

    public function __construct(string $id, string $password)
    {
        $this->id = str_replace('-', '', $id);
        $this->password = hash('sha512', $password);
    }

    public static function getInstance(string $id, string $password): self
    {
        return new self($id, $password);
    }

    public function response(string $endpoint, array $data = []): self
    {
        $url = self::API_URL . '/' . ltrim($endpoint, '/');

        $postData = http_build_query(array_merge([
            'id'   => $this->id,
            'pass' => $this->password,
        ], $data));

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_TIMEOUT        => self::TIMEOUT,
        ]);

        $result = curl_exec($ch);

        if ($result === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new LGUPlusException('LGU+ API 통신 오류: ' . $error);
        }

        curl_close($ch);

        $this->lastResponse = json_decode($result, true) ?? [];

        return $this;
    }

    public function isSuccess(): bool
    {
        return ($this->lastResponse['SVC_RT'] ?? null) === '0000';
    }

    public function getData(): ?array
    {
        return $this->lastResponse['DATAS'] ?? null;
    }

    public function getListInfo(): ?array
    {
        return $this->lastResponse['LISTINFO'] ?? null;
    }

    public function getError(): ?array
    {
        if ($this->isSuccess()) return null;
        return [
            'code'    => $this->lastResponse['SVC_RT'] ?? null,
            'message' => $this->lastResponse['SVC_MSG'] ?? null,
        ];
    }

    // --- Typed Model Getters ---

    /**
     * 오류 정보를 ApiError 모델로 반환
     * 성공 시 null 반환
     */
    public function getApiError(): ?ApiError
    {
        if ($this->isSuccess()) return null;
        return ApiError::fromResponse($this->lastResponse ?? []);
    }

    /**
     * 주소록 DATAS 를 Contact 모델 배열로 반환
     *
     * @return Contact[]
     */
    public function getContacts(): array
    {
        $datas = $this->lastResponse['DATAS'] ?? null;
        if (!is_array($datas)) return [];
        return array_map(
            fn(array $item) => Contact::fromArray($item),
            $datas
        );
    }

    /**
     * 녹취 목록 DATAS 를 Recording 모델 배열로 반환
     *
     * @return Recording[]
     */
    public function getRecordings(): array
    {
        $datas = $this->lastResponse['DATAS'] ?? null;
        if (!is_array($datas)) return [];
        return array_map(
            fn(array $item) => Recording::fromArray($item),
            $datas
        );
    }

    /**
     * 통화 이력 DATAS 를 CallHistory 모델 배열로 반환
     *
     * @return CallHistory[]
     */
    public function getCallHistories(): array
    {
        $datas = $this->lastResponse['DATAS'] ?? null;
        if (!is_array($datas)) return [];
        return array_map(
            fn(array $item) => CallHistory::fromArray($item),
            $datas
        );
    }

    /**
     * SMS 발송 결과를 SmsResult 모델로 반환
     * DATAS 가 없으면 null 반환
     */
    public function getSmsResult(): ?SmsResult
    {
        $datas = $this->lastResponse['DATAS'] ?? null;
        if (!is_array($datas)) return null;
        return SmsResult::fromArray($datas);
    }

    /**
     * 사용자 정보를 UserInfo 모델로 반환
     * DATAS 가 없으면 null 반환
     */
    public function getUserInfo(): ?UserInfo
    {
        $datas = $this->lastResponse['DATAS'] ?? null;
        if (!is_array($datas)) return null;
        return UserInfo::fromArray($datas);
    }

    /**
     * 통화 체널 상태를 ChannelStatus 모델로 반환
     * DATAS 가 없으면 null 반환
     */
    public function getChannelStatus(): ?ChannelStatus
    {
        $datas = $this->lastResponse['DATAS'] ?? null;
        if (!is_array($datas)) return null;
        return ChannelStatus::fromArray($datas);
    }

    /**
     * 페이지 정보를 ListInfo 모델로 반환
     * LISTINFO 가 없으면 null 반환
     */
    public function getPaginationInfo(): ?ListInfo
    {
        $raw = $this->lastResponse['LISTINFO'] ?? null;
        if ($raw === null) return null;
        return ListInfo::fromArray($raw);
    }

    /**
     * 수신 URL 알림 설정 조회 결과를 RingCallbackResult 모델로 반환
     * DATAS 가 없으면 null 반환
     */
    public function getRingCallbackResult(): ?RingCallbackResult
    {
        $datas = $this->lastResponse['DATAS'] ?? null;
        if (!is_array($datas)) return null;
        return RingCallbackResult::fromArray($datas);
    }


    // --- 이지 API ---

    /**
     * 전화 걸기
     */
    public function call(string $destNumber): self
    {
        $destNumber = str_replace('-', '', $destNumber);
        return $this->response('/clickdial', [
            'destnumber' => $destNumber,
        ]);
    }

    /**
     * 전화 끊기
     */
    public function hangup(): self
    {
        return $this->response('/hangup');
    }

    /**
     * 녹취 시작
     */
    public function startRecording(): self
    {
        return $this->response('/startrecord');
    }

    /**
     * 녹취 중지
     */
    public function stopRecording(): self
    {
        return $this->response('/stoprecord');
    }

    /**
     * 통화 보류 시작
     */
    public function startHold(): self
    {
        return $this->response('/starthold');
    }

    /**
     * 통화 보류 중지
     */
    public function stopHold(): self
    {
        return $this->response('/stophold');
    }

    /**
     * 회의 통화 연동
     * @param array $destNumbers
     */
    public function conference(array $destNumbers): self
    {
        $destNumbers = array_map(fn($num) => str_replace('-', '', $num), $destNumbers);
        $destNumberStr = implode(',', $destNumbers);

        return $this->response('/conference', [
            'destnumbers' => $destNumberStr,
        ]);
    }

    /**
     * 주소록 한 페이지 조회
     */
    public function addressList(int $page = 1): self
    {
        return $this->response('/addresslist', [
            'page' => ($page > 0) ? $page : 1,
        ]);
    }

    /**
     * 녹취 목록 조회
     */
    public function recordList(int $page = 1): self
    {
        return $this->response('/recordlist', [
            'page' => ($page > 0) ? $page : 1,
        ]);
    }

    /**
     * 통화 이력 조회
     *
     * @param int    $page     페이지 번호
     * @param string $callType 수신/발신 구분 ('inbound' | 'outbound')
     */
    public function callHistory(int $page = 1, string $callType = 'outbound'): self
    {
        return $this->response('/callhistory', [
            'page'     => ($page > 0) ? $page : 1,
            'calltype' => $callType,
        ]);
    }

    /**
     * SMS / LMS 발송
     *
     * @param string|array $destNumbers 수신자 번호 (10개 이내, 배열 또는 콤마 구분 문자열)
     * @param string       $message     발송 내용 (SMS: 80 Byte 이내, LMS: 720 Byte 이내)
     */
    public function smsSend($destNumbers, string $message): self
    {
        if (is_array($destNumbers)) {
            $destNumbers = array_map(fn($n) => str_replace('-', '', $n), $destNumbers);
            $destNumbers = implode(',', $destNumbers);
        } else {
            $destNumbers = str_replace('-', '', $destNumbers);
        }

        return $this->response('/smssend', [
            'destnumber' => $destNumbers,
            'smsmsg'     => $message,
        ]);
    }

    /**
     * 착신전환 설정
     *
     * @param string $destNumber 전환할 대상 번호
     */
    public function setForward(string $destNumber): self
    {
        $destNumber = str_replace('-', '', $destNumber);
        return $this->response('/setforward', [
            'destnumber' => $destNumber,
        ]);
    }

    /**
     * 착신전환 해제
     */
    public function stopForward(): self
    {
        return $this->response('/stopforward');
    }

    /**
     * 사용자 정보 조회
     */
    public function userInfo(): self
    {
        return $this->response('/userinfo');
    }

    /**
     * 폰 상태 조회
     * isSuccess() 로 정상 여부 확인
     */
    public function phoneStatus(): self
    {
        return $this->response('/phonestatus');
    }

    /**
     * 통화 상태 조회
     */
    public function channelStatus(): self
    {
        return $this->response('/channelstatus');
    }

    /**
     * 전화 수신 시 웹훅(URL 알림) 등록
     *
     * 수신(RING) 이벤트 발생 시 지정한 URL 로 HTTP 호출을 보냅니다.
     * - https 미지원, http 만 가능
     * - callbackurl 의 page 는 확장자 포함 필수 (예: /call.php)
     * - 실제 호출은 호스트명이 아닌 callbackhost IP 로 수행됨
     *
     * @param string $callbackUrl  호출 URL ('http://호스트명/page' 또는 '/page' 형식)
     * @param string $callbackHost 호출 대상 서버 IP (IPv4, 예: 10.10.10.2)
     * @param string $callbackPort 호출 대상 포트 (예: 80, 8080)
     */
    public function registerCallWebhook(string $callbackUrl, string $callbackHost, string $callbackPort): self
    {
        return $this->response('/setringcallback', [
            'callbackurl'  => $callbackUrl,
            'callbackhost' => $callbackHost,
            'callbackport' => $callbackPort,
        ]);
    }

    /**
     * 전화 수신 URL 알림 설정 정보 조회
     */
    public function getRingCallback(): self
    {
        return $this->response('/getringcallback');
    }

    /**
     * 전화 수신 URL 알림 설정 정보 삭제
     * isSuccess() 로 성공 여부 확인
     */
    public function deleteRingCallback(): self
    {
        return $this->response('/delringcallback');
    }

    /**
     * 문자 메시지 수신 시 웹훅(URL 알림) 등록
     *
     * SMS 수신 이벤트 발생 시 지정한 URL 로 HTTP 호출을 보냅니다.
     * - https 미지원, http 만 가능
     * - callbackurl 의 page 는 확장자 포함 필수 (예: /sms.php)
     * - 실제 호출은 호스트명이 아닌 callbackhost IP 로 수행됨
     *
     * @param string $callbackUrl  호출 URL ('http://호스트명/page' 또는 '/page' 형식)
     * @param string $callbackHost 호출 대상 서버 IP (IPv4, 예: 10.10.10.2)
     * @param string $callbackPort 호출 대상 포트 (예: 80, 8080)
     */
    public function registerSmsWebhook(string $callbackUrl, string $callbackHost, string $callbackPort): self
    {
        return $this->response('/setsmscallback', [
            'callbackurl'  => $callbackUrl,
            'callbackhost' => $callbackHost,
            'callbackport' => $callbackPort,
        ]);
    }

    /**
     * 문자 메시지 수신 URL 알림 설정 정보 조회
     * getRingCallbackResult() 로 결과 파싱
     */
    public function getSmsCallback(): self
    {
        return $this->response('/getsmscallback');
    }

    /**
     * 문자 메시지 수신 URL 알림 설정 정보 삭제
     * isSuccess() 로 성공 여부 확인
     */
    public function deleteSmsCallback(): self
    {
        return $this->response('/delsmscallback');
    }

    /**
     * 수신 문자 메시지 목록 조회
     */
    public function getRecvSmsList(int $page = 1): self
    {
        return $this->response('/getrecvsmslist', [
            'page' => ($page > 0) ? $page : 1,
        ]);
    }

    /**
     * 수신 문자 목록 DATAS 를 ReceivedSms 모델 배열로 반환
     *
     * @return ReceivedSms[]
     */
    public function getReceivedSmsMessages(): array
    {
        $datas = $this->lastResponse['DATAS'] ?? null;
        if (!is_array($datas)) return [];
        return array_map(
            fn(array $item) => ReceivedSms::fromArray($item),
            $datas
        );
    }
    /**
     * 착신전환 유형별 설정
     *
     * @param string $destNumber 착신전환 번호 (예: '07011112222')
     * @param string $ftype      착신전환 유형 (LGUPlus::FORWARD_TYPE_* 상수 사용 권장)
     *                           '0':무조건, '1':받지않을경우, '2':통화중일경우,
     *                           '3':동시연결, '4':통화중또는무응답시
     */
    public function setForwardType(string $destNumber, string $ftype): self
    {
        $destNumber = str_replace('-', '', $destNumber);
        return $this->response('/setforwardtype', [
            'destnumber' => $destNumber,
            'ftype'      => $ftype,
        ]);
    }

    /**
     * 착신전환 유형별 설정정보 조회
     */
    public function getForwardType(): self
    {
        return $this->response('/getforwardtype');
    }

    /**
     * 착신전환 유형별 설정정보를 ForwardTypeInfo 모델로 반환
     * DATAS 가 없으면 null 반환
     */
    public function getForwardTypeInfo(): ?ForwardTypeInfo
    {
        $datas = $this->lastResponse['DATAS'] ?? null;
        if (!is_array($datas)) return null;
        return ForwardTypeInfo::fromArray($datas);
    }

    /**
     * 외부인입번호별 수신 통화이력 조회
     *
     * @param int $page       페이지 번호 (기본: 1)
     * @param int $numPerPage 페이지당 목록 수 (기본: 10, 최대: 200)
     */
    public function getInboundCall(int $page = 1, int $numPerPage = 10): self
    {
        return $this->response('/getinboundcall', [
            'page'         => ($page > 0) ? $page : 1,
            'num_per_page' => min(max(1, $numPerPage), 200),
        ]);
    }

    /**
     * 외부인입 수신 통화이력 DATAS 를 InboundCall 모델 배열로 반환
     *
     * @return InboundCall[]
     */
    public function getInboundCalls(): array
    {
        $datas = $this->lastResponse['DATAS'] ?? null;
        if (!is_array($datas)) return [];
        return array_map(
            fn(array $item) => InboundCall::fromArray($item),
            $datas
        );
    }

    /**
     * 개인 녹취 파일 다운로드 (유료 부가서비스 B타입 전용)
     *
     * 성공 시 오디오 파일의 바이너리 데이터를 반환하며, isSuccess() 는 true 를 반환합니다.
     * 실패 시 null 을 반환하고, isSuccess() / getApiError() 로 오류를 확인할 수 있습니다.
     *
     * 주의: 동일 계정으로 동시 다중 다운로드 또는 연속 다운로드를 수행하면
     *       다운로드가 실패하거나 차단될 수 있습니다.
     *       반드시 1개 완료 후 수 초 간격을 두고 다음 파일을 요청하십시오.
     *
     * @param  string $filename Recording::$fileName 값 (예: '0854-010XXXX9087_20190813134606_mix.wav')
     * @return string|null      성공 시 오디오 바이너리 데이터, 실패 시 null
     * @throws LGUPlusException cURL 통신 오류 발생 시
     */
    public function downloadRecording(string $filename): ?string
    {
        $url = self::API_URL . '/recorddownload';

        $postData = http_build_query([
            'id'       => $this->id,
            'pass'     => $this->password,
            'filename' => $filename,
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_TIMEOUT        => 120, // 파일 다운로드이므로 여유 있는 타임아웃
        ]);

        $result = curl_exec($ch);

        if ($result === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new LGUPlusException('LGU+ 녹취 다운로드 통신 오류: ' . $error);
        }

        curl_close($ch);

        // 오류 시 JSON 응답, 성공 시 오디오 바이너리
        $decoded = json_decode($result, true);
        if (is_array($decoded)) {
            // JSON 오류 응답
            $this->lastResponse = $decoded;
            return null;
        }

        // 성공: 바이너리 수신 완료
        $this->lastResponse = ['SVC_RT' => '0000', 'SVC_MSG' => 'OK'];
        return $result;
    }

    /**
     * 개인 녹취 파일 삭제 (유료 부가서비스 B타입 전용)
     *
     * 성공 여부는 isSuccess() 로 확인합니다.
     * 실패 시 getApiError() 로 오류 상세를 확인할 수 있습니다.
     *
     * @param string $filename Recording::$fileName 값 (예: '0854-010XXXX9087_20190813134606_mix.wav')
     */
    public function deleteRecording(string $filename): self
    {
        return $this->response('/recordremove', [
            'filename' => $filename,
        ]);
    }

    /**
     * 패스워드 초기화를 위한 인증코드 요청
     *
     * 성공 시 인증코드가 등록된 IP폰 단말기로 전송되며, SESSIONID 를 반환합니다.
     * SESSIONID 는 5분간 유효하며 비밀번호 변경 요청 시 함께 사용합니다.
     * pass 파라미터는 선택 사항입니다.
     *
     * 유의 사항: IP폰 사전 등록이 필요합니다.
     */
    public function getAuthCode(): self
    {
        return $this->response('/getauthcode');
    }

    /**
     * 인증코드 요청 결과를 AuthCodeResult 모델로 반환
     * DATAS 가 없으면 null 반환
     */
    public function getAuthCodeResult(): ?AuthCodeResult
    {
        $datas = $this->lastResponse['DATAS'] ?? null;
        if (!is_array($datas)) return null;
        return AuthCodeResult::fromArray($datas);
    }

    /**
     * 패스워드 초기화
     *
     * getAuthCode() 로 발급받은 SESSIONID 와 단말에 수신된 6자리 authcode 를 함께 전달합니다.
     * newpass 는 평문으로 전송합니다(서버가 SHA512 해시 처리).
     *
     * 신규 비밀번호 규칙 (클라이언트에서 사전 검증 권장):
     *  - 문자 + 숫자 + 특수문자 조합 (연속 불가)
     *  - 8~10자리
     *  - 허용 특수문자: ~ ! @ $ % ^ * ( ) - _ , . ?  (14종)
     *  - 이전 3회 비밀번호 재사용 불가
     *
     * 유의 사항: IP폰 사전 등록이 필요합니다.
     *
     * @param string $sessionId getAuthCode() 결과의 AuthCodeResult::$sessionId
     * @param string $newPass   변경할 신규 비밀번호 (평문)
     * @param string $authCode  단말에 수신된 6자리 인증코드
     */
    public function resetPassword(string $sessionId, string $newPass, string $authCode): self
    {
        return $this->response('/setauthchange', [
            'sessionid' => $sessionId,
            'newpass'   => $newPass,
            'authcode'  => $authCode,
        ]);
    }

    /**
     * 패스워드 초기화 결과를 PasswordResetResult 모델로 반환
     * DATAS 가 없으면 null 반환
     */
    public function getPasswordResetResult(): ?PasswordResetResult
    {
        $datas = $this->lastResponse['DATAS'] ?? null;
        if (!is_array($datas)) return null;
        return PasswordResetResult::fromArray($datas);
    }

    /**
     * 패스워드 변경
     *
     * 현재 비밀번호(생성자에 전달한 pass)가 인증에 사용됩니다.
     * newpass 는 평문으로 전달합니다(서버에서 SHA512 처리).
     *
     * 신규 비밀번호 규칙 (클라이언트에서 사전 검증 권장):
     *  - 문자 + 숫자 + 특수문자 조합 (연속 불가)
     *  - 8~10자리
     *  - 허용 특수문자: ~ ! @ $ % ^ * ( ) - _ , . ?  (14종)
     *
     * 성공 시 isSuccess() === true, DATAS 는 결과 문구 문자열입니다.
     * 실패 시 getApiError() 로 오류를 확인할 수 있습니다.
     *
     * @param string $newPass 변경할 신규 비밀번호 (평문)
     */
    public function changePassword(string $newPass): self
    {
        return $this->response('/updateauth', [
            'newpass' => $newPass,
        ]);
    }

    /**
     * 패스워드 변경 기한 만료일 연장
     *
     * 비밀번호 만료가 임박했을 때 변경 없이 만료일만 연장합니다.
     * 성공 시 isSuccess() === true, DATAS 는 결과 문구 문자열입니다.
     * 실패 시 getApiError() 로 오류를 확인할 수 있습니다.
     */
    public function extendPasswordExpiry(): self
    {
        return $this->response('/extendauth');
    }
}
