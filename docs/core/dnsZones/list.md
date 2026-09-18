# `$bunny->core->dnsZones->list()`

> Core Platform API · `GET /dnszone`

List DNS Zones

## Signature

```php
public function list(
    int $page = 1,
    int $perPage = 100,
    ?string $search = null,
    ?DnsViewType $view = null,
): Page
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$page` | `int` | no | The page to return, starting at 1. |
| `$perPage` | `int` | no | The number of items per page. |
| `$search` | `string\|null` | no | The search term that will be used to filter the results |
| `$view` | `DnsViewType\|null` | no |  |

## Returns

`Page<DnsZone>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->dnsZones->list();
```
