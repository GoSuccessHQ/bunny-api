# `$bunny->shield->zones->list()`

> Shield API · `GET /shield/shield-zones`

Get all of your Shield Zone Configurations

## Signature

```php
public function list(int $page = 1, int $perPage = 100): Page
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$page` | `int` | no | The page to return, starting at 1. |
| `$perPage` | `int` | no | The number of items per page. |

## Returns

`Page<ShieldZone>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->zones->list();
```
