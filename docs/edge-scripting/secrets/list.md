# `$bunny->edgeScripting->secrets->list()`

> Edge Scripting API · `GET /compute/script/{id}/secrets`

List Secrets

## Signature

```php
public function list(int $scriptId): array
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$scriptId` | `int` | yes | The ID of the script to list secrets |

## Returns

`list<EdgeScriptSecret>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->edgeScripting->secrets->list(scriptId: 123);
```
