# `$bunny->shield->customRules->delete()`

> Shield API · `DELETE /shield/waf/custom-rule/{id}`

Delete a custom WAF rule

## Signature

```php
public function delete(int $id): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the Custom WAF Rule that should be deleted. |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->shield->customRules->delete(id: 123);
```
