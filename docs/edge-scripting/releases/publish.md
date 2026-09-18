# `$bunny->edgeScripting->releases->publish()`

> Edge Scripting API · `POST /compute/script/{id}/publish`

Publish Release

## Signature

```php
public function publish(int $scriptId, ?string $note = null): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$scriptId` | `int` | yes | Publishes the current code as a release |
| `$note` | `string\|null` | no |  |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->edgeScripting->releases->publish(scriptId: 123);
```
