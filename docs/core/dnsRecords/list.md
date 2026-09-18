# `dnsRecords->list()`

> Core Platform API · `GET /dnszone/{zoneId}/records`

List DNS Zone Records

## Signature

```php
public function list(
    int $zoneId,
    int $page = 1,
    int $perPage = 100,
    ?DnsRecordType $type = null,
    ?string $search = null,
): Page
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$zoneId` | `int` | yes | The ID of the DNS Zone |
| `$page` | `int` | no | The page to return, starting at 1. |
| `$perPage` | `int` | no | The number of items per page. |
| `$type` | `DnsRecordType\|null` | no | The type of DNS Record |
| `$search` | `string\|null` | no |  |

## Returns

`Page<DnsRecord>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->dnsRecords->list(zoneId: 123);
```
