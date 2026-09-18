# `$bunny->shield->metrics->apiGuardian()`

> Shield API · `GET /shield/metrics/shield-zone/{shieldZoneId}/api-guardian`

Get API Guardian metrics for the specified Shield Zone

## Signature

```php
public function apiGuardian(int $shieldZoneId): ApiGuardianMetrics
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield Zone. |

## Returns

`ApiGuardianMetrics`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->metrics->apiGuardian(shieldZoneId: 123);
```
