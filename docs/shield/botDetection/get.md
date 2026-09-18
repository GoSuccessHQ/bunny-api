# `$bunny->shield->botDetection->get()`

> Shield API · `GET /shield/shield-zone/{shieldZoneId}/bot-detection`

Your current Bot Detection configuration

## Signature

```php
public function get(int $shieldZoneId): BotDetectionConfiguration
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes |  |

## Returns

`BotDetectionConfiguration`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->botDetection->get(shieldZoneId: 123);
```
