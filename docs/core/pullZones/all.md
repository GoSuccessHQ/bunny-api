# `pullZones->all()`

> Core Platform API · `GET /pullzone`

Iterate lazily over every item of list(), across all pages.

## Signature

```php
public function all(
    ?string $search = null,
    ?bool $includeCertificate = null,
    int $perPage = 1000,
): Paginator
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$search` | `string\|null` | no | The search term that will be used to filter the results |
| `$includeCertificate` | `bool\|null` | no | Determines if the result hostnames should contain the SSL certificate |
| `$perPage` | `int` | no | The number of items per page. |

## Returns

`Paginator<PullZone>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

foreach ($bunny->core->pullZones->all() as $item) {
    // ...
}
```
