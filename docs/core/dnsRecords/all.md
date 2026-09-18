# `dnsRecords->all()`

> Core Platform API · `GET /dnszone/{zoneId}/records`

Iterate lazily over every item of list(), across all pages.

## Signature

```php
public function all(
    int $zoneId,
    ?DnsRecordType $type = null,
    ?string $search = null,
    int $perPage = 1000,
): Paginator
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$zoneId` | `int` | yes | The ID of the DNS Zone |
| `$type` | `DnsRecordType\|null` | no | The type of DNS Record |
| `$search` | `string\|null` | no |  |
| `$perPage` | `int` | no | The number of items per page. |

## Returns

`Paginator<DnsRecord>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

foreach ($bunny->core->dnsRecords->all(zoneId: 123) as $item) {
    // ...
}
```
