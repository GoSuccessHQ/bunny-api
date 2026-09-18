# `$bunny->shield->waf->recommendation()`

> Shield API · `GET /shield/waf/rules/review-triggered/ai-recommendation/{shieldZoneId}/{ruleId}`

Retrieve an AI recommendation for a triggered WAF rule

## Signature

```php
public function recommendation(int $shieldZoneId, string $ruleId): TriggeredRuleRecommendation
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield Zone for the Triggered Rule. |
| `$ruleId` | `string` | yes | The ID of the Triggered Rule. |

## Returns

`TriggeredRuleRecommendation`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->waf->recommendation(shieldZoneId: 123, ruleId: '00000000-0000-0000-0000-000000000000');
```
