# `$bunny->shield->waf->rulesByPlan()`

> Shield API · `GET /shield/waf/rules/plan-segmentation`

Retrieve WAF rules segmented by subscription plan

## Signature

```php
public function rulesByPlan(): array
```

## Returns

`list<WafRulesByPlan>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->waf->rulesByPlan();
```
