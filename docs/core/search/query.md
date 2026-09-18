# `search->query()`

> Core Platform API · `GET /search`

Global Search

## Signature

```php
public function query(?string $search = null, ?int $from = null, ?int $size = null): SearchResults
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$search` | `string\|null` | no |  |
| `$from` | `int\|null` | no |  |
| `$size` | `int\|null` | no |  |

## Returns

`SearchResults`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->search->query();
```
