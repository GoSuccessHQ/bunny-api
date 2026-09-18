# `$bunny->edgeScripting->releases->publishRelease()`

> Edge Scripting API · `POST /compute/script/{id}/publish/{uuid}`

Publish Release

## Signature

```php
public function publishRelease(int $scriptId, string $uuid, ?string $note = null): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$scriptId` | `int` | yes | Publishes the current code as a release |
| `$uuid` | `string` | yes | The UUID of the script release that will be published |
| `$note` | `string\|null` | no |  |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->edgeScripting->releases->publishRelease(scriptId: 123, uuid: '00000000-0000-0000-0000-000000000000');
```
