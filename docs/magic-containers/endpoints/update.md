# `$bunny->magicContainers->endpoints->update()`

> Magic Containers API · `PUT /apps/{appId}/endpoints/{endpointId}`

Update Application Endpoint

Update an existing endpoint for given application

## Signature

```php
public function update(string $appId, string $endpointId, EndpointRequest $endpoint): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes | The ID of the application |
| `$endpointId` | `string` | yes | The display name of the endpoint to update |
| `$endpoint` | `EndpointRequest` | yes |  |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\MagicContainers\Model\EndpointRequest;

$bunny = new Bunny('your-api-key');

$bunny->magicContainers->endpoints->update(appId: '00000000-0000-0000-0000-000000000000', endpointId: '00000000-0000-0000-0000-000000000000', endpoint: new EndpointRequest(/* ... */));
```
