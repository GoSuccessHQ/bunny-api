# `$bunny->shield->rateLimits->create()`

> Shield API · `POST /shield/rate-limit`

Create a Rate Limit for your Shield Zone

## Signature

```php
public function create(
    int $shieldZoneId,
    ?string $ruleName = null,
    ?string $ruleDescription = null,
    ?RateLimitRuleConfiguration $ruleConfiguration = null,
): RateLimitRule
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes |  |
| `$ruleName` | `string\|null` | no |  |
| `$ruleDescription` | `string\|null` | no |  |
| `$ruleConfiguration` | `RateLimitRuleConfiguration\|null` | no |  |

## Returns

`RateLimitRule`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->rateLimits->create(shieldZoneId: 123);
```
