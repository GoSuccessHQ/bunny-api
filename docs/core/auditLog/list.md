# `$bunny->core->auditLog->list()`

> Core Platform API · `GET /user/audit/{date}`

## Signature

```php
public function list(
    DateTimeInterface $date,
    ?array $product = null,
    ?array $resourceType = null,
    ?array $resourceId = null,
    ?array $actorId = null,
    ?LogOrdering $order = null,
    ?string $continuationToken = null,
    ?int $limit = null,
): Page
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
| `$continuationToken` | `string\|null` | no | The position returned by the previous page; null for the first page. |
| `$limit` | `int\|null` | no | The number of items per page. |

## Returns

`Page<AuditLogEntry>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->auditLog->list(date: new DateTimeImmutable('-7 days'));
```
