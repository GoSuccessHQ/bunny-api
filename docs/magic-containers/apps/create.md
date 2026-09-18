# `$bunny->magicContainers->apps->create()`

> Magic Containers API · `POST /apps`

Add Application

Creates a new application with the specified configuration including containers, volumes, region settings, and autoscaling.

## Signature

```php
public function create(AddApplicationRequest $application): string
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$application` | `AddApplicationRequest` | yes |  |

## Returns

`string`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\MagicContainers\Model\AddApplicationRequest;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->apps->create(application: new AddApplicationRequest(/* ... */));
```
