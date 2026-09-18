# `$bunny->magicContainers->apps->get()`

> Magic Containers API · `GET /apps/{appId}`

Get application

## Signature

```php
public function get(string $appId): Application
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes |  |

## Returns

`Application`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->apps->get(appId: '00000000-0000-0000-0000-000000000000');
```
