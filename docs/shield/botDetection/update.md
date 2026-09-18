# `$bunny->shield->botDetection->update()`

> Shield API · `PATCH /shield/shield-zone/{shieldZoneId}/bot-detection`

Update your current Bot Detection configuration

## Signature

```php
public function update(
    int $shieldZoneId,
    ?BotDetectionExecutionMode $executionMode = null,
    ?RequestIntegrityConfiguration $requestIntegrity = null,
    ?IpAddressConfiguration $ipAddress = null,
    ?BrowserFingerprintConfiguration $browserFingerprint = null,
): BotDetectionConfiguration
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes |  |
| `$executionMode` | `BotDetectionExecutionMode\|null` | no | 0 = LogOnly 1 = Challenge |
| `$requestIntegrity` | `RequestIntegrityConfiguration\|null` | no |  |
| `$ipAddress` | `IpAddressConfiguration\|null` | no |  |
| `$browserFingerprint` | `BrowserFingerprintConfiguration\|null` | no |  |

## Returns

`BotDetectionConfiguration`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->botDetection->update(shieldZoneId: 123);
```
