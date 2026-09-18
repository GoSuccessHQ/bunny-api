# `$bunny->edgeScripting->secrets->create()`

> Edge Scripting API · `POST /compute/script/{id}/secrets`

Add Secret

## Signature

```php
public function create(int $scriptId, string $name, string $secret): EdgeScriptSecret
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$scriptId` | `int` | yes | The ID of the script that will have the secret added |
| `$name` | `string` | yes |  |
| `$secret` | `string` | yes |  |

## Returns

`EdgeScriptSecret`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->edgeScripting->secrets->create(scriptId: 123, name: 'example', secret: 'example');
```
