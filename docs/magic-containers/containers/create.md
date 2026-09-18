# `$bunny->magicContainers->containers->create()`

> Magic Containers API · `POST /apps/{appId}/containers`

Add Container Template

Adds a new container template to an application.

## Signature

```php
public function create(string $appId, AddContainerRequest $container): ContainerTemplate
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes | The ID of the application |
| `$container` | `AddContainerRequest` | yes |  |

## Returns

`ContainerTemplate`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\MagicContainers\Model\AddContainerRequest;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->containers->create(appId: '00000000-0000-0000-0000-000000000000', container: new AddContainerRequest(/* ... */));
```
