# `$bunny->shield->customRules->get()`

> Shield API · `GET /shield/waf/custom-rule/{id}`

Retrieve a specific custom WAF rule

## Signature

```php
public function get(int $id): CustomRule
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the Custom WAF Rule. |

## Returns

`CustomRule`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->customRules->get(id: 123);
```
