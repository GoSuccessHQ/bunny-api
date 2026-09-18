# `$bunny->edgeScripting->variables->get()`

> Edge Scripting API · `GET /compute/script/{id}/variables/{variableId}`

Get Variable

## Signature

```php
public function get(int $scriptId, int $variableId): EdgeScriptVariable
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$scriptId` | `int` | yes | The ID of the script that contains the variable |
| `$variableId` | `int` | yes | The ID of the Environment Variable that should be returned |

## Returns

`EdgeScriptVariable`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->edgeScripting->variables->get(scriptId: 123, variableId: 123);
```
