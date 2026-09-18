# `$bunny->magicContainers->endpoints->create()`

> Magic Containers API · `POST /apps/{appId}/containers/{containerId}/endpoints`

Add application endpoint

Add CDN or Anycast endpoint to a container of given application.

## Signature

```php
public function create(string $appId, string $containerId, EndpointRequest $endpoint): string
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes | The ID of the application |
| `$containerId` | `string` | yes | The ID of the container template |
| `$endpoint` | `EndpointRequest` | yes |  |

## Returns

`string`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\MagicContainers\Model\EndpointRequest;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->endpoints->create(appId: '00000000-0000-0000-0000-000000000000', containerId: '00000000-0000-0000-0000-000000000000', endpoint: new EndpointRequest(/* ... */));
```
