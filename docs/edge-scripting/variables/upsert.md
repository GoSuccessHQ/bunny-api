# `$bunny->edgeScripting->variables->upsert()`

> Edge Scripting API · `PUT /compute/script/{id}/variables`

Upsert Variable

Returns null if bunny.net answers 204 No Content, which the specification allows.

## Signature

```php
public function upsert(
    int $scriptId,
    string $name,
    ?bool $required = null,
    ?string $defaultValue = null,
): ?EdgeScriptVariable
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$scriptId` | `int` | yes | The ID of the script which will have the variable upserted |
| `$name` | `string` | yes |  |
| `$required` | `bool\|null` | no |  |
| `$defaultValue` | `string\|null` | no |  |

## Returns

`?EdgeScriptVariable`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->edgeScripting->variables->upsert(scriptId: 123, name: 'example');
```
