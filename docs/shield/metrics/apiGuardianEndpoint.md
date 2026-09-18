# `$bunny->shield->metrics->apiGuardianEndpoint()`

> Shield API · `GET /shield/metrics/shield-zone/{shieldZoneId}/api-guardian/endpoint/{endpointId}`

Get metrics for a specific API Guardian endpoint within the specified Shield Zone

## Signature

```php
public function apiGuardianEndpoint(int $shieldZoneId, int $endpointId): ApiGuardianEndpointMetrics
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield Zone. |
| `$endpointId` | `int` | yes | The ID of the API Guardian Endpoint. |

## Returns

`ApiGuardianEndpointMetrics`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->metrics->apiGuardianEndpoint(shieldZoneId: 123, endpointId: 123);
```
