# `$bunny->shield->waf->rules()`

> Shield API · `GET /shield/waf/rules/{shieldZoneId}`

Retrieve all available WAF rules for a Shield Zone

## Signature

```php
public function rules(int $shieldZoneId): array
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield Zone for the WAF Rules. |

## Returns

`list<WafRuleMainGroup>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->waf->rules(shieldZoneId: 123);
```
