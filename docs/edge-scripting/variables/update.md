# `$bunny->edgeScripting->variables->update()`

> Edge Scripting API · `POST /compute/script/{id}/variables/{variableId}`

Update Variable

## Signature

```php
public function update(
    int $scriptId,
    int $variableId,
    ?string $defaultValue = null,
    ?bool $required = null,
): EdgeScriptVariable
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$scriptId` | `int` | yes | The ID of the script that contains the variable |
| `$variableId` | `int` | yes | The ID of the Environment Variable that will be updated |
| `$defaultValue` | `string\|null` | no |  |
| `$required` | `bool\|null` | no |  |

## Returns

`EdgeScriptVariable`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->edgeScripting->variables->update(scriptId: 123, variableId: 123);
```
