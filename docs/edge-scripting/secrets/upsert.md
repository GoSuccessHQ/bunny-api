# `$bunny->edgeScripting->secrets->upsert()`

> Edge Scripting API · `PUT /compute/script/{id}/secrets`

Upsert Secret

Returns the secret if it was created, and null if an existing secret was updated (204 No Content).

## Signature

```php
public function upsert(int $scriptId, string $name, string $secret): ?EdgeScriptSecret
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$scriptId` | `int` | yes | The ID of the script that will have the secret upserted |
| `$name` | `string` | yes |  |
| `$secret` | `string` | yes |  |

## Returns

`?EdgeScriptSecret`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->edgeScripting->secrets->upsert(scriptId: 123, name: 'example', secret: 'example');
```
