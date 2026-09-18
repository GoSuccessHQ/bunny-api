# `$bunny->edgeScripting->scripts->update()`

> Edge Scripting API · `POST /compute/script/{id}`

Update Edge Script

## Signature

```php
public function update(int $id, ?string $name = null, ?EdgeScriptType $scriptType = null): EdgeScript
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the script that will be updated |
| `$name` | `string\|null` | no |  |
| `$scriptType` | `EdgeScriptType\|null` | no | 0 = DNS 1 = CDN 2 = Middleware |

## Returns

`EdgeScript`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->edgeScripting->scripts->update(id: 123);
```
