# `$bunny->magicContainers->apps->patch()`

> Magic Containers API · `PATCH /apps/{appId}`

Patch Application

Partially updates an existing application using JSON Merge Patch semantics. Only provided fields will be updated; existing fields not included in the request will remain unchanged. For arrays (containers, volumes, endpoints), items with matching IDs will be updated, items without IDs will be added as new, and items not included will be deleted.

## Signature

```php
public function patch(string $appId, PatchApplicationRequest $changes): string
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes | The ID of the application to update |
| `$changes` | `PatchApplicationRequest` | yes |  |

## Returns

`string`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\MagicContainers\Model\PatchApplicationRequest;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->apps->patch(appId: '00000000-0000-0000-0000-000000000000', changes: new PatchApplicationRequest(/* ... */));
```
