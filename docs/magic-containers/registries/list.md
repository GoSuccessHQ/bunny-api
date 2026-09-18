# `$bunny->magicContainers->registries->list()`

> Magic Containers API · `GET /registries`

List Container Registries

Lists all container registries configured for the authenticated user.

## Signature

```php
public function list(): array
```

## Returns

`list<ContainerRegistry>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->registries->list();
```
