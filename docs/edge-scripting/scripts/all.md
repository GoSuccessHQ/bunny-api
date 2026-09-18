# `$bunny->edgeScripting->scripts->all()`

> Edge Scripting API · `GET /compute/script`

Iterate lazily over every item of list(), across all pages.

## Signature

```php
public function all(
    ?array $type = null,
    ?string $search = null,
    ?bool $includeLinkedPullZones = null,
    ?int $integrationId = null,
    int $perPage = 1000,
): Paginator
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$type` | `list<EdgeScriptType>\|null` | no | Filter by edge script type |
| `$search` | `string\|null` | no | The search term that will be used to filter the results |
| `$includeLinkedPullZones` | `bool\|null` | no | Include linked pullzones |
| `$integrationId` | `int\|null` | no | Filter by linked integration |
| `$perPage` | `int` | no | The number of items per page. |

## Returns

`Paginator<EdgeScript>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

foreach ($bunny->edgeScripting->scripts->all() as $item) {
    // ...
}
```
