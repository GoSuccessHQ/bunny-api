# `$bunny->magicContainers->apps->delete()`

> Magic Containers API · `DELETE /apps/{appId}`

Delete Application

Marks the application for deletion and enqueues cleanup of all associated resources. Returns immediately; deletion is processed asynchronously.

## Signature

```php
public function delete(string $appId): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes |  |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->magicContainers->apps->delete(appId: '00000000-0000-0000-0000-000000000000');
```
