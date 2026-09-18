# `$bunny->core->pullZones->removeCertificate()`

> Core Platform API · `DELETE /pullzone/{id}/removeCertificate`

Remove Certificate

## Signature

```php
public function removeCertificate(int $id, string $hostname): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the requested Pull Zone |
| `$hostname` | `string` | yes | The hostname from which the certificate will be removed |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->pullZones->removeCertificate(id: 123, hostname: 'cdn.example.com');
```
