# `$bunny->edgeScripting->variables->delete()`

> Edge Scripting API · `DELETE /compute/script/{id}/variables/{variableId}`

Delete Variable

## Signature

```php
public function delete(int $scriptId, int $variableId): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$scriptId` | `int` | yes | The ID of the script that contains the Environment Variable |
| `$variableId` | `int` | yes | The ID of the Environment Variable that should be deleted |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->edgeScripting->variables->delete(scriptId: 123, variableId: 123);
```
