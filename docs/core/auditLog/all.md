# `auditLog->all()`

> Core Platform API · `GET /user/audit/{date}`

Iterate lazily over every item of list(), across all pages.

## Signature

```php
public function all(
    DateTimeInterface $date,
    ?array $product = null,
    ?array $resourceType = null,
    ?array $resourceId = null,
    ?array $actorId = null,
    ?LogOrdering $order = null,
    ?int $limit = null,
): Paginator
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$date` | `DateTimeInterface` | yes |  |
| `$product` | `list<string>\|null` | no |  |
| `$resourceType` | `list<string>\|null` | no |  |
| `$resourceId` | `list<string>\|null` | no |  |
| `$actorId` | `list<string>\|null` | no |  |
| `$order` | `LogOrdering\|null` | no |  |
| `$limit` | `int\|null` | no | The number of items per page. |

## Returns

`Paginator<AuditLogEntry>`

## Example

```php
use DateTimeImmutable;
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

foreach ($bunny->core->auditLog->all(date: new DateTimeImmutable('-7 days')) as $item) {
    // ...
}
```
