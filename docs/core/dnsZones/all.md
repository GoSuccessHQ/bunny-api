# `dnsZones->all()`

> Core Platform API · `GET /dnszone`

Iterate lazily over every item of list(), across all pages.

## Signature

```php
public function all(?string $search = null, ?DnsViewType $view = null, int $perPage = 1000): Paginator
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$search` | `string\|null` | no | The search term that will be used to filter the results |
| `$view` | `DnsViewType\|null` | no |  |
| `$perPage` | `int` | no | The number of items per page. |

## Returns

`Paginator<DnsZone>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

foreach ($bunny->core->dnsZones->all() as $item) {
    // ...
}
```
