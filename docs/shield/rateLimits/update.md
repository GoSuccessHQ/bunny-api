# `$bunny->shield->rateLimits->update()`

> Shield API · `PATCH /shield/rate-limit/{id}`

Update a Rate Limit configuration on your Shield Zone

## Signature

```php
public function update(
    int $id,
    ?string $ruleName = null,
    ?string $ruleDescription = null,
    ?RateLimitRuleConfiguration $ruleConfiguration = null,
): RateLimitRule
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the Rate Limit you would like to update. |
| `$ruleName` | `string\|null` | no |  |
| `$ruleDescription` | `string\|null` | no |  |
| `$ruleConfiguration` | `RateLimitRuleConfiguration\|null` | no |  |

## Returns

`RateLimitRule`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->rateLimits->update(id: 123);
```
