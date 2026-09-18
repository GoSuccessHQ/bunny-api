# `$bunny->magicContainers->endpoints->list()`

> Magic Containers API · `GET /apps/{appId}/endpoints`

List application endpoints

List endpoints from all containers for given application

## Signature

```php
public function list(string $appId): array
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes | The ID of the application |

## Returns

`list<EndpointListItem>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->endpoints->list(appId: '00000000-0000-0000-0000-000000000000');
```
