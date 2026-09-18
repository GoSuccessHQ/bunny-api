# `$bunny->magicContainers->endpoints->delete()`

> Magic Containers API · `DELETE /apps/{appId}/endpoints/{endpointId}`

Delete application endpoint

Delete endpoint of a container for given application.

## Signature

```php
public function delete(string $appId, string $endpointId): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes | The ID of the application |
| `$endpointId` | `string` | yes | The ID of the endpoint to delete |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->magicContainers->endpoints->delete(appId: '00000000-0000-0000-0000-000000000000', endpointId: '00000000-0000-0000-0000-000000000000');
```
