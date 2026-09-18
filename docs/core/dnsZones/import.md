# `dnsZones->import()`

> Core Platform API · `POST /dnszone/{zoneId}/import`

Import records from a BIND zone file.

The file is sent as plain text, as bunny.net's own CLI does; the specification does not document the request body. Record types other than A, AAAA, CNAME, MX, TXT, SRV, CAA and PTR are skipped.

## Signature

```php
public function import(int $zoneId, Stream|string $zoneFile): DnsZoneImportResult
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$zoneId` | `int` | yes | The ID of the DNS zone |
| `$zoneFile` | `string\|Stream` | yes | The zone file contents. |

## Returns

`DnsZoneImportResult`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Http\Stream;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->dnsZones->import(zoneId: 123, zoneFile: Stream::fromFile('path/to/file'));
```
