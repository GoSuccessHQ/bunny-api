# `$bunny->edgeScripting->secrets->delete()`

> Edge Scripting API · `DELETE /compute/script/{id}/secrets/{secretId}`

Delete Secret

## Signature

```php
public function delete(int $scriptId, int $secretId): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$scriptId` | `int` | yes | The ID of the script that contains the secret |
| `$secretId` | `int` | yes | The ID of the secret that should be deleted |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->edgeScripting->secrets->delete(scriptId: 123, secretId: 123);
```
