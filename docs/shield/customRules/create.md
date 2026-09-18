# `$bunny->shield->customRules->create()`

> Shield API · `POST /shield/waf/custom-rule`

Create a new custom WAF rule

## Signature

```php
public function create(
    int $shieldZoneId,
    ?string $ruleName = null,
    ?string $ruleDescription = null,
    ?CustomRuleConfiguration $ruleConfiguration = null,
): CustomRule
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes |  |
| `$ruleName` | `string\|null` | no |  |
| `$ruleDescription` | `string\|null` | no |  |
| `$ruleConfiguration` | `CustomRuleConfiguration\|null` | no |  |

## Returns

`CustomRule`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->customRules->create(shieldZoneId: 123);
```
