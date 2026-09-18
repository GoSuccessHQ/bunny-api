# `pullZones->updatePrivateKeyType()`

> Core Platform API · `POST /pullzone/{id}/updatePrivateKeyType`

Change hostname private key type

## Signature

```php
public function updatePrivateKeyType(int $id, string $hostname, PrivateKeyType $keyType): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The hostname that the private key type will be updated on |
| `$hostname` | `string` | yes |  |
| `$keyType` | `PrivateKeyType` | yes | 0 = Ecdsa 1 = Rsa |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Core\Enum\PrivateKeyType;

$bunny = new Bunny('your-api-key');

$bunny->core->pullZones->updatePrivateKeyType(id: 123, hostname: 'cdn.example.com', keyType: PrivateKeyType::Ecdsa);
```
