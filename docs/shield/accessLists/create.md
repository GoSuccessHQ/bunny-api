# `$bunny->shield->accessLists->create()`

> Shield API · `POST /shield/shield-zone/{shieldZoneId}/access-lists`

Create a new Custom Access List associated with a Shield Zone

## Signature

```php
public function create(
    int $shieldZoneId,
    ?string $name,
    AccessListType $type,
    ?string $content,
    ?string $description = null,
    ?string $checksum = null,
): CustomAccessList
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield Zone to which the Custom Access List will be associated. |
| `$name` | `string\|null` | yes | The display name for the new access list. |
| `$type` | `AccessListType` | yes | 0 = IP 1 = CIDR 2 = ASN 3 = Country 4 = Organization 5 = JA4 |
| `$content` | `string\|null` | yes | The initial content for the access list with entries separated by newlines. |
| `$description` | `string\|null` | no | Optional description of the access list's purpose or contents. |
| `$checksum` | `string\|null` | no | SHA-256 checksum of the content for integrity verification. If null, it will be automatically calculated. |

## Returns

`CustomAccessList`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Shield\Enum\AccessListType;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->accessLists->create(shieldZoneId: 123, name: 'example', type: AccessListType::IP, content: 'example');
```
