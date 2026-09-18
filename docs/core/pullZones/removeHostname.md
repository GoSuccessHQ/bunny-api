# `$bunny->core->pullZones->removeHostname()`

> Core Platform API · `DELETE /pullzone/{id}/removeHostname`

Remove Custom Hostname

## Signature

```php
public function removeHostname(int $id, string $hostname): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the requested Pull Zone |
| `$hostname` | `string` | yes | The hostname that will be removed |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->pullZones->removeHostname(id: 123, hostname: 'cdn.example.com');
```
