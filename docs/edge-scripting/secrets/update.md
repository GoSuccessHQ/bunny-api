# `$bunny->edgeScripting->secrets->update()`

> Edge Scripting API · `POST /compute/script/{id}/secrets/{secretId}`

Update Secret

## Signature

```php
public function update(int $scriptId, int $secretId, string $secret): EdgeScriptSecret
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$scriptId` | `int` | yes | The ID of the script that will have the secret updated |
| `$secretId` | `int` | yes | The ID of the secret that will be updated |
| `$secret` | `string` | yes |  |

## Returns

`EdgeScriptSecret`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->edgeScripting->secrets->update(scriptId: 123, secretId: 123, secret: 'example');
```
