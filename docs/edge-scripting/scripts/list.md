# `$bunny->edgeScripting->scripts->list()`

> Edge Scripting API · `GET /compute/script`

List Edge Scripts

## Signature

```php
public function list(
    ?array $type = null,
    int $page = 1,
    int $perPage = 100,
    ?string $search = null,
    ?bool $includeLinkedPullZones = null,
    ?int $integrationId = null,
): Page
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$type` | `list<EdgeScriptType>\|null` | no | Filter by edge script type |
| `$page` | `int` | no | The page to return, starting at 1. |
| `$perPage` | `int` | no | The number of items per page. |
| `$search` | `string\|null` | no | The search term that will be used to filter the results |
| `$includeLinkedPullZones` | `bool\|null` | no | Include linked pullzones |
| `$integrationId` | `int\|null` | no | Filter by linked integration |

## Returns

`Page<EdgeScript>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->edgeScripting->scripts->list();
```
