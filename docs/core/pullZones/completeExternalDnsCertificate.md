# `$bunny->core->pullZones->completeExternalDnsCertificate()`

> Core Platform API · `POST /pullzone/completeExternalDnsCertificate`

Complete External DNS Certificate

## Signature

```php
public function completeExternalDnsCertificate(string $hostname): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$hostname` | `string` | yes |  |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->pullZones->completeExternalDnsCertificate(hostname: 'cdn.example.com');
```
