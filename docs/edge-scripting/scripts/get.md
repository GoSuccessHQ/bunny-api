# `$bunny->edgeScripting->scripts->get()`

> Edge Scripting API · `GET /compute/script/{id}`

Get Edge Script

## Signature

```php
public function get(int $id): EdgeScript
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the script that will be returned |

## Returns

`EdgeScript`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->edgeScripting->scripts->get(id: 123);
```
