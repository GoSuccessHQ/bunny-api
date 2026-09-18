# `$bunny->shield->customRules->update()`

> Shield API · `PATCH /shield/waf/custom-rule/{id}`

Update an existing custom WAF rule

## Signature

```php
public function update(
    int $id,
    ?string $ruleName = null,
    ?string $ruleDescription = null,
    ?CustomRuleConfiguration $ruleConfiguration = null,
): CustomRule
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the Custom WAF Rule you would like to update. |
| `$ruleName` | `string\|null` | no |  |
| `$ruleDescription` | `string\|null` | no |  |
| `$ruleConfiguration` | `CustomRuleConfiguration\|null` | no |  |

## Returns

`CustomRule`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->customRules->update(id: 123);
```
