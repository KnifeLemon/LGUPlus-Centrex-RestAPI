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

> `https` 미지원, `http`만 가능합니다.

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

### 비밀번호 변경

```php
$lgu->changePassword('새비밀번호');
```

### 비밀번호 유효기간 연장

```php
$lgu->extendPasswordExpiry();
```

### 비밀번호 초기화 (인증코드 방식)

```php
// 1단계: 인증코드 요청
$lgu->getAuthCode();
$authResult = $lgu->getAuthCodeResult(); // AuthCodeResult

// 2단계: 인증코드로 비밀번호 재설정
$lgu->resetPassword($authResult->sessionId, '새비밀번호', '인증코드6자리');
$resetResult = $lgu->getPasswordResetResult(); // PasswordResetResult
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
