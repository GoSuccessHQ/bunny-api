# `$bunny->shield->zones->update()`

> Shield API · `PATCH /shield/shield-zone`

Update your Shield Zone configuration

## Signature

```php
public function update(int $shieldZoneId, ?ShieldZoneSettings $shieldZone = null): ShieldZoneUpdate
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes |  |
| `$shieldZone` | `ShieldZoneSettings\|null` | no |  |

## Returns

`ShieldZoneUpdate`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->zones->update(shieldZoneId: 123);
```
