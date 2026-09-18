# `$bunny->magicContainers->nodes->plain()`

> Magic Containers API · `GET /nodes/plain`

List Node IPs (Plain)

Lists all node IP addresses in the Magic Containers network as a flat list.

## Signature

```php
public function plain(): array
```

## Returns

`list<string>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->nodes->plain();
```
