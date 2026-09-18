# `$bunny->shield->zones->createUnderAttack()`

> Shield API · `POST /shield/shield-zone/under-attack`

Create a Shield Zone in under-attack mode for your PullZone

## Signature

```php
public function createUnderAttack(int $pullZoneId, ShieldPlanType $planType): ShieldZoneSetup
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$pullZoneId` | `int` | yes |  |
| `$planType` | `ShieldPlanType` | yes | 0 = Basic 1 = Advanced 2 = Business 3 = Enterprise |

## Returns

`ShieldZoneSetup`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Shield\Enum\ShieldPlanType;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->zones->createUnderAttack(pullZoneId: 123, planType: ShieldPlanType::Basic);
```
