# `$bunny->shield->zones->create()`

> Shield API · `POST /shield/shield-zone`

Create a Shield Zone for your PullZone

## Signature

```php
public function create(
    int $pullZoneId,
    ?ShieldZoneSettings $shieldZone = null,
    ?array $accessLists = null,
    ?BotDetectionExecutionMode $botDetectionExecutionMode = null,
    ?UploadScanningScannerMode $csamScanningMode = null,
    ?UploadScanningScannerMode $antivirusScanningMode = null,
): ShieldZoneSetup
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$pullZoneId` | `int` | yes |  |
| `$shieldZone` | `ShieldZoneSettings\|null` | no |  |
| `$accessLists` | `?array` | no |  |
| `$botDetectionExecutionMode` | `BotDetectionExecutionMode\|null` | no | 0 = LogOnly 1 = Challenge |
| `$csamScanningMode` | `UploadScanningScannerMode\|null` | no | 0 = Disabled 1 = Log 2 = Block |
| `$antivirusScanningMode` | `UploadScanningScannerMode\|null` | no | 0 = Disabled 1 = Log 2 = Block |

## Returns

`ShieldZoneSetup`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->zones->create(pullZoneId: 123);
```
