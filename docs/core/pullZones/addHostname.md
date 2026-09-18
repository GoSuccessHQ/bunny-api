# `$bunny->core->pullZones->addHostname()`

> Core Platform API · `POST /pullzone/{id}/addHostname`

Add Custom Hostname

## Signature

```php
public function addHostname(int $id, string $hostname): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the requested Pull Zone |
| `$hostname` | `string` | yes | The hostname that will be added |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->pullZones->addHostname(id: 123, hostname: 'cdn.example.com');
```
