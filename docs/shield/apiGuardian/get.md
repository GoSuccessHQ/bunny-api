# `$bunny->shield->apiGuardian->get()`

> Shield API · `GET /shield/shield-zone/{shieldZoneId}/api-guardian`

Get the API Guardian configuration and endpoints.

## Signature

```php
public function get(int $shieldZoneId): ApiGuardian
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes |  |

## Returns

`ApiGuardian`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->apiGuardian->get(shieldZoneId: 123);
```
