# `$bunny->shield->zones->getByPullZone()`

> Shield API · `GET /shield/shield-zone/get-by-pullzone/{pullZoneId}`

Get Singular Shield Zone Configuration for PullZone

## Signature

```php
public function getByPullZone(int $pullZoneId): ShieldZone
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$pullZoneId` | `int` | yes | The ID of the PullZone. |

## Returns

`ShieldZone`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->zones->getByPullZone(pullZoneId: 123);
```
