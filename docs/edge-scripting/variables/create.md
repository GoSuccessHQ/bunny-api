# `$bunny->edgeScripting->variables->create()`

> Edge Scripting API · `POST /compute/script/{id}/variables/add`

Add Variable

## Signature

```php
public function create(
    int $scriptId,
    string $name,
    bool $required,
    ?string $defaultValue = null,
): EdgeScriptVariable
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$scriptId` | `int` | yes | The ID of the script that will be updated |
| `$name` | `string` | yes |  |
| `$required` | `bool` | yes |  |
| `$defaultValue` | `string\|null` | no |  |

## Returns

`EdgeScriptVariable`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->edgeScripting->variables->create(scriptId: 123, name: 'example', required: true);
```
