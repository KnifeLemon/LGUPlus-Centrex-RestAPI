# LGU+ Centrex REST API PHP Client

LG U+ Centrex REST API를 PHP에서 간편하게 사용할 수 있는 클라이언트 라이브러리입니다.

## 요구 사항

- PHP >= 7.4
- ext-curl
- ext-json

## 설치

```bash
composer require knifelemon/lguplus-centrex-restapi
```

## 초기화

```php
use LGUPlus\Centrex\LGUPlus;
use LGUPlus\Centrex\LGUPlusException;

$lgu = new LGUPlus('내선번호', '비밀번호');
// 또는
$lgu = LGUPlus::getInstance('내선번호', '비밀번호');
```

> 내선번호의 하이픈(`-`)은 자동으로 제거됩니다. 비밀번호는 SHA-512로 해싱되어 전송됩니다.

> **주의:** 이 라이브러리는 LG U+ Centrex 유료 서비스 가입자 전용입니다. API 사용 전 LG U+에서 REST API 부가서비스를 신청해야 합니다.

---

## 통화 제어

### 전화 걸기

```php
$lgu->call('010-1234-5678');

if ($lgu->isSuccess()) {
    // 성공
} else {
    $error = $lgu->getApiError();
    echo $error; // "[1003] 인증 오류"
}
```

### 전화 끊기

```php
$lgu->hangup();
```

### 녹취

```php
$lgu->startRecording(); // 녹취 시작
$lgu->stopRecording();  // 녹취 중지
```

### 통화 보류

```php
$lgu->startHold(); // 보류 시작
$lgu->stopHold();  // 보류 해제
```

### 다자간 통화

```php
$lgu->conference(['010-1111-2222', '010-3333-4444']);
```

---

## 주소록

```php
// 단일 페이지 조회
$lgu->addressList(1);
$contacts = $lgu->getContacts(); // Contact[]
$pageInfo = $lgu->getPaginationInfo(); // ListInfo

// 페이지 순회 (Generator)
foreach ($lgu->eachAddressPage() as $page => $contacts) {
    foreach ($contacts as $contact) {
        echo $contact->name . ' / ' . $contact->phone1 . PHP_EOL;
    }
}

// 전체 주소록 한 번에 가져오기
$allContacts = $lgu->getAllAddresses(); // Contact[]
```

---

## 녹취 목록

```php
$lgu->recordList(1);
$recordings = $lgu->getRecordings(); // Recording[]

foreach ($recordings as $rec) {
    echo $rec->fileName . ' (' . $rec->fileSize . ' bytes)' . PHP_EOL;
}
```

### 녹취 파일 다운로드 (유료 B타입 전용)

> **주의사항**
> - 동일 계정으로 동시 또는 연속 다운로드 시 실패하거나 차단될 수 있습니다.
> - 반드시 1개 완료 후 **수 초 간격**을 두고 다음 파일을 요청하십시오.
> - 반환값이 `null`이면 `isSuccess()` / `getApiError()`로 오류를 확인하십시오.

```php
$binary = $lgu->downloadRecording('0854-010XXXX9087_20190813134606_mix.wav');

if ($binary !== null) {
    file_put_contents('recording.wav', $binary);
} else {
    echo $lgu->getApiError();
}
```

### 녹취 파일 삭제

```php
$lgu->deleteRecording('0854-010XXXX9087_20190813134606_mix.wav');
```

---

## 통화 이력

```php
$lgu->callHistory(1, 'outbound'); // 'inbound' | 'outbound'
$histories = $lgu->getCallHistories(); // CallHistory[]
```

### 외부인입 수신 통화이력

```php
$lgu->getInboundCall(1, 10);
$calls = $lgu->getInboundCalls(); // InboundCall[]

foreach ($calls as $call) {
    echo $call->src . ' → ' . $call->dst;
    echo $call->isAnswered() ? ' (수신)' : ' (부재중)';
    echo PHP_EOL;
}
```

---

## SMS

### 발송

> **발송 제한**
> - 수신자 최대 **10개**
> - SMS: **80 Byte** 이내 (초과 시 LMS 처리)
> - LMS: **720 Byte** 이내
> - 잔여 건수가 0이면 발송 불가 (`SmsResult::$restCount` 확인)

```php
// 단일 수신자
$lgu->smsSend('010-9999-8888', '안녕하세요!');
$result = $lgu->getSmsResult(); // SmsResult

echo '잔여 건수: ' . $result->restCount;

// 복수 수신자 (최대 10개)
$lgu->smsSend(['010-1111-2222', '010-3333-4444'], '공지사항입니다.');
```

### 수신 문자 목록 조회

```php
$lgu->getRecvSmsList(1);
$messages = $lgu->getReceivedSmsMessages(); // ReceivedSms[]

foreach ($messages as $sms) {
    echo $sms->src . ': ' . $sms->message . PHP_EOL;
}
```

---

## 착신전환

```php
// 착신전환 설정
$lgu->setForward('070-1111-2222');

// 착신전환 해제
$lgu->stopForward();

// 착신전환 유형별 설정
$lgu->setForwardType('070-1111-2222', LGUPlus::FORWARD_TYPE_NO_ANSWER);

// 설정 조회
$lgu->getForwardType();
$info = $lgu->getForwardTypeInfo(); // ForwardTypeInfo
echo $info->isActive() ? '착신전환 활성' : '비활성';
```

착신전환 유형 상수:

| 상수 | 값 | 설명 |
|---|---|---|
| `FORWARD_TYPE_ALWAYS` | `'0'` | 무조건 |
| `FORWARD_TYPE_NO_ANSWER` | `'1'` | 받지 않을 경우 |
| `FORWARD_TYPE_BUSY` | `'2'` | 통화 중일 경우 |
| `FORWARD_TYPE_SIMULTANEOUS` | `'3'` | 동시 연결 |
| `FORWARD_TYPE_BUSY_OR_NO_ANSWER` | `'4'` | 통화 중 또는 무응답 시 |

---

## 상태 조회

```php
// 사용자 정보
$lgu->userInfo();
$user = $lgu->getUserInfo(); // UserInfo
echo $user->name . ' (' . $user->exten . ')';

// 폰 상태
$lgu->phoneStatus();
echo $lgu->isSuccess() ? '정상' : '오프라인';

// 통화 채널 상태
$lgu->channelStatus();
$status = $lgu->getChannelStatus(); // ChannelStatus
```

---

## 웹훅 (URL 알림)

> **주의사항**
> - `https` 미지원, **`http`만** 가능합니다.
> - `callbackurl`의 경로에는 반드시 **확장자 포함** 필요 (예: `/call.php`, `/sms.php`)
> - 실제 HTTP 호출은 `callbackurl`의 호스트명이 아닌 **`callbackhost` IP**로 직접 수행됩니다.
> - 웹훅은 **GET 방식**으로 전달됩니다. `WebhookPayload::fromGet($_GET)`으로 수신하십시오.
> - 등록은 계정당 하나만 유지됩니다. 재등록 시 기존 설정이 덮어쓰여집니다.

### 전화 수신 웹훅

```php
// 등록
$lgu->registerCallWebhook('http://example.com/call.php', '10.10.10.2', '80');

// 조회
$lgu->getRingCallback();
$info = $lgu->getRingCallbackResult(); // RingCallbackResult

// 삭제
$lgu->deleteRingCallback();
```

### SMS 수신 웹훅

```php
// 등록
$lgu->registerSmsWebhook('http://example.com/sms.php', '10.10.10.2', '80');

// 조회
$lgu->getSmsCallback();

// 삭제
$lgu->deleteSmsCallback();
```

### 웹훅 수신 처리

```php
use LGUPlus\Centrex\Models\WebhookPayload;

// GET 파라미터로 수신
$payload = WebhookPayload::fromGet($_GET);

if ($payload->isCall()) {
    echo '전화 수신: ' . $payload->sender . ' → ' . $payload->receiver;
} elseif ($payload->isSms()) {
    echo 'SMS 수신: ' . $payload->message;
}
```

---

## 비밀번호 관리

> **비밀번호 규칙** (변경/초기화 모두 동일)
> - 문자 + 숫자 + 특수문자 **혼합 필수** (동일 문자 연속 불가)
> - **8~10자리**
> - 허용 특수문자 (14종): `` ~ ! @ $ % ^ * ( ) - _ , . ? ``
> - 이전 **3회** 사용한 비밀번호 재사용 불가
> - `changePassword()` / `resetPassword()`에 전달하는 비밀번호는 **평문**으로 전달합니다 (라이브러리가 SHA-512 처리하지 않음 — 서버에서 직접 처리)

### 비밀번호 변경

```php
$lgu->changePassword('새비밀번호');
```

### 비밀번호 유효기간 연장

```php
$lgu->extendPasswordExpiry();
```

### 비밀번호 초기화 (인증코드 방식)

> - IP폰 단말기 **사전 등록** 필요 (LG U+에 신청)
> - 인증코드는 등록된 IP폰으로 전송되며 **6자리**, **SESSIONID는 5분간 유효**
> - 인증코드는 **1회만** 사용 가능 (재사용 시 오류코드 `1203`)

```php
// 1단계: 인증코드 요청
$lgu->getAuthCode();
$authResult = $lgu->getAuthCodeResult(); // AuthCodeResult
// $authResult->sessionId — 2단계에서 사용 (5분 이내)
// $authResult->tryCnt   — 남은 인증 시도 횟수

// 2단계: 인증코드로 비밀번호 재설정
$lgu->resetPassword($authResult->sessionId, '새비밀번호', '인증코드6자리');
$resetResult = $lgu->getPasswordResetResult(); // PasswordResetResult
```

---

## 주요 오류 코드

| 코드 | 설명 |
|---|---|
| `0000` | 성공 |
| `1001` | 필수 파라미터 누락 |
| `1002` | 파라미터 형식 오류 |
| `1003` | 인증 오류 (id/pass 불일치) |
| `1004` | 비밀번호 오류 횟수 초과 |
| `1007` | 초기 비밀번호 변경 필요 |
| `1008` | 비밀번호 만료 (변경 또는 연장 필요) |
| `1202` | 비밀번호 변경 한도 초과 |
| `1203` | 인증코드 이미 사용됨 |
| `1204` | 인증코드 오류 또는 만료 |
| `2001` | 등록되지 않은 IP |
| `2002` | 사용 권한 없음 |
| `3001` | 명령 처리 오류 |
| `3002` | 메시지 길이 초과 |
| `3003` | 수신자 수 초과 |
| `3004` | SMS 잔여 건수 없음 |
| `4002` | 조회 데이터 없음 |
| `4004` | 통화 채널 없음 |
| `9999` | 기타 오류 |

전체 오류 설명은 `ErrorCode::describe($code)`로 확인할 수 있습니다.

```php
use LGUPlus\Centrex\Models\ErrorCode;

echo ErrorCode::describe('1008'); // '비밀번호가 만료되었습니다'
```

---

## API 응답 메서드 요약

| 메서드 | 반환 타입 | 설명 |
|---|---|---|
| `isSuccess()` | `bool` | 성공 여부 (`SVC_RT === '0000'`) |
| `getApiError()` | `?ApiError` | 오류 정보, 성공 시 `null` |
| `getContacts()` | `Contact[]` | 주소록 목록 |
| `getPaginationInfo()` | `?ListInfo` | 페이지 정보 |
| `getRecordings()` | `Recording[]` | 녹취 목록 |
| `getCallHistories()` | `CallHistory[]` | 통화 이력 목록 |
| `getInboundCalls()` | `InboundCall[]` | 외부인입 수신 이력 |
| `getSmsResult()` | `?SmsResult` | SMS 발송 결과 |
| `getReceivedSmsMessages()` | `ReceivedSms[]` | 수신 문자 목록 |
| `getUserInfo()` | `?UserInfo` | 사용자 정보 |
| `getChannelStatus()` | `?ChannelStatus` | 통화 채널 상태 |
| `getRingCallbackResult()` | `?RingCallbackResult` | URL 알림 설정 정보 |
| `getForwardTypeInfo()` | `?ForwardTypeInfo` | 착신전환 유형별 설정 정보 |
| `getAuthCodeResult()` | `?AuthCodeResult` | 인증코드 요청 결과 |
| `getPasswordResetResult()` | `?PasswordResetResult` | 비밀번호 재설정 결과 |

---

## 일반 주의사항

- 모든 API 호출은 **동기(synchronous)** 방식입니다. 대량 처리 시 적절한 딜레이를 두십시오.
- API 서버(`centrex.uplus.co.kr`)에 대한 접근은 **허용된 IP에서만** 가능합니다 (오류코드 `2001`).
- 동일 `id`/`pass`로 **동시 다중 호출**을 자제하십시오 — 특히 녹취 다운로드.
- 응답 후 반드시 `isSuccess()`를 확인하십시오. API가 HTTP 200을 반환해도 처리 실패일 수 있습니다.
- 비밀번호 오류가 반복되면 계정이 잠길 수 있습니다 (오류코드 `1004`).

---

## 예외 처리

cURL 통신 오류 발생 시 `LGUPlusException`이 던져집니다.

```php
try {
    $lgu->call('010-1234-5678');
} catch (LGUPlusException $e) {
    echo '통신 오류: ' . $e->getMessage();
}
```

---

## 라이선스

MIT
