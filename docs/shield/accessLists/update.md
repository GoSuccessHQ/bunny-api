# `$bunny->shield->accessLists->update()`

> Shield API · `PATCH /shield/shield-zone/{shieldZoneId}/access-lists/{id}`

Update the specified Custom Access List associated with a Shield Zone

## Signature

```php
public function update(
    int $shieldZoneId,
    int $id,
    ?string $name = null,
    ?string $content = null,
    ?string $checksum = null,
): CustomAccessList
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield Zone to which the Custom Access List belongs. |
| `$id` | `int` | yes | The ID of the Custom Access List to update. |
| `$name` | `string\|null` | no | The new display name for the access list. If null, the current name is preserved. |
| `$content` | `string\|null` | no | The new content for the access list with entries separated by newlines. If null, the current content is preserved. |
| `$checksum` | `string\|null` | no | SHA-256 checksum of the new content for integrity verification. If null, it will be automatically calculated. |

## Returns

`CustomAccessList`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->accessLists->update(shieldZoneId: 123, id: 123);
```
