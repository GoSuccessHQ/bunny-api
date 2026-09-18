# `$bunny->edgeScripting->scripts->code()`

> Edge Scripting API · `GET /compute/script/{id}/code`

Get Code

## Signature

```php
public function code(int $id): EdgeScriptCode
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the code for which the script that will be returned |

## Returns

`EdgeScriptCode`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->edgeScripting->scripts->code(id: 123);
```
