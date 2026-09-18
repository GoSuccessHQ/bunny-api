# `$bunny->shield->metrics->wafRule()`

> Shield API · `GET /shield/metrics/shield-zone/{shieldZoneId}/waf-rule/{ruleId}`

Get metrics for a specific WAF Rule within the specified Shield Zone

## Signature

```php
public function wafRule(int $shieldZoneId, string $ruleId): WafRuleMetrics
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield Zone. |
| `$ruleId` | `string` | yes | The ID of the WAF Rule. |

## Returns

`WafRuleMetrics`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->metrics->wafRule(shieldZoneId: 123, ruleId: '00000000-0000-0000-0000-000000000000');
```
