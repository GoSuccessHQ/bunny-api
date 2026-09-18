# `$bunny->shield->rateLimits->delete()`

> Shield API · `DELETE /shield/rate-limit/{id}`

Delete a Rate Limit on your Shield Zone

## Signature

```php
public function delete(int $id): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the Rate Limit that should be deleted. |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->shield->rateLimits->delete(id: 123);
```
