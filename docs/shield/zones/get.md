# `$bunny->shield->zones->get()`

> Shield API · `GET /shield/shield-zone/{shieldZoneId}`

Get Singular Shield Zone Configuration

## Signature

```php
public function get(int $shieldZoneId): ShieldZone
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield Zone. |

## Returns

`ShieldZone`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->zones->get(shieldZoneId: 123);
```
