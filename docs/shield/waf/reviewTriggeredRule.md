# `$bunny->shield->waf->reviewTriggeredRule()`

> Shield API · `POST /shield/waf/rules/review-triggered/{shieldZoneId}`

Review and update the action of a triggered WAF rule

## Signature

```php
public function reviewTriggeredRule(int $shieldZoneId, string $ruleId, ReviewActionType $action): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield Zone for the Triggered Rule. |
| `$ruleId` | `string` | yes |  |
| `$action` | `ReviewActionType` | yes | 0 = Ignore 1 = LogOnly 2 = DisableRule |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Shield\Enum\ReviewActionType;

$bunny = new Bunny('your-api-key');

$bunny->shield->waf->reviewTriggeredRule(shieldZoneId: 123, ruleId: '00000000-0000-0000-0000-000000000000', action: ReviewActionType::Ignore);
```
