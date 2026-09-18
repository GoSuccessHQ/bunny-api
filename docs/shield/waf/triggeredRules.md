# `$bunny->shield->waf->triggeredRules()`

> Shield API · `GET /shield/waf/rules/review-triggered/{shieldZoneId}`

Review all triggered WAF rules for the specified Shield Zone

## Signature

```php
public function triggeredRules(int $shieldZoneId): TriggeredRules
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield Zone for the Triggered Rules. |

## Returns

`TriggeredRules`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->waf->triggeredRules(shieldZoneId: 123);
```
