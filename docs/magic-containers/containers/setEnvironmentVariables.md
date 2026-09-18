# `$bunny->magicContainers->containers->setEnvironmentVariables()`

> Magic Containers API · `PUT /apps/{appId}/containers/{containerId}/env`

Set Container Environment Variables

Replaces all environment variables for a container template. All existing environment variables will be removed and replaced with the provided set.

## Signature

```php
public function setEnvironmentVariables(string $appId, string $containerId, array $variables): ContainerTemplate
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes | The ID of the application |
| `$containerId` | `string` | yes | The ID of the container template |
| `$variables` | `array` | yes |  |

## Returns

`ContainerTemplate`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->containers->setEnvironmentVariables(appId: '00000000-0000-0000-0000-000000000000', containerId: '00000000-0000-0000-0000-000000000000', variables: [/* ... */]);
```
