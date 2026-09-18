# `$bunny->core->dnsZones->export()`

> Core Platform API · `GET /dnszone/{id}/export`

Export the records of a zone as a BIND zone file.

Bunny-specific records (pull zone, redirect, script) are not exported.

## Signature

```php
public function export(int $id): string
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the DNS zone |

## Returns

`string`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->dnsZones->export(id: 123);
```
