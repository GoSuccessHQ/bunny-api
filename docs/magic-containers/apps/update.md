# `$bunny->magicContainers->apps->update()`

> Magic Containers API · `PUT /apps/{appId}`

Update Application

Updates an existing application with full replacement of all configuration fields.

## Signature

```php
public function update(string $appId, AddApplicationRequest $application): string
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes | The ID of the application to update |
| `$application` | `AddApplicationRequest` | yes |  |

## Returns

`string`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\MagicContainers\Model\AddApplicationRequest;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->apps->update(appId: '00000000-0000-0000-0000-000000000000', application: new AddApplicationRequest(/* ... */));
```
