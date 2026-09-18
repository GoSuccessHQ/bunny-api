# `$bunny->shield->accessLists->list()`

> Shield API · `GET /shield/shield-zone/{shieldZoneId}/access-lists`

Get all Access Lists available for a Shield Zone

## Signature

```php
public function list(int $shieldZoneId): AccessListOverview
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield Zone to which the Access Lists belong. |

## Returns

`AccessListOverview`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->accessLists->list(shieldZoneId: 123);
```
