# `$bunny->core->pullZones->setForceSsl()`

> Core Platform API · `POST /pullzone/{id}/setForceSSL`

Set Force SSL

## Signature

```php
public function setForceSsl(int $id, string $hostname, bool $forceSSL): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the requested Pull Zone |
| `$hostname` | `string` | yes | The hostname that will be updated |
| `$forceSSL` | `bool` | yes | Set to true to force SSL on the given pull zone hostname |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->pullZones->setForceSsl(id: 123, hostname: 'cdn.example.com', forceSSL: true);
```
