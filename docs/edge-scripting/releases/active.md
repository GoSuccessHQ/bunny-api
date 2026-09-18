# `$bunny->edgeScripting->releases->active()`

> Edge Scripting API · `GET /compute/script/{id}/releases/active`

Get Active Release

## Signature

```php
public function active(int $scriptId): EdgeScriptRelease
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$scriptId` | `int` | yes | The ID of the script for which active release would be returned |

## Returns

`EdgeScriptRelease`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->edgeScripting->releases->active(scriptId: 123);
```
