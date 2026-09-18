# `pullZones->list()`

> Core Platform API · `GET /pullzone`

List Pull Zones

## Signature

```php
public function list(
    int $page = 1,
    int $perPage = 100,
    ?string $search = null,
    ?bool $includeCertificate = null,
): Page
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$page` | `int` | no | The page to return, starting at 1. |
| `$perPage` | `int` | no | The number of items per page. |
| `$search` | `string\|null` | no | The search term that will be used to filter the results |
| `$includeCertificate` | `bool\|null` | no | Determines if the result hostnames should contain the SSL certificate |

## Returns

`Page<PullZone>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->pullZones->list();
```
