# `$bunny->edgeScripting->scripts->setCode()`

> Edge Scripting API · `POST /compute/script/{id}/code`

Set Code

## Signature

```php
public function setCode(int $id, string $code): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the code for which the script that will be returned |
| `$code` | `string` | yes |  |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->edgeScripting->scripts->setCode(id: 123, code: 'example');
```
