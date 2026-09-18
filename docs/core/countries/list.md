# `$bunny->core->countries->list()`

> Core Platform API · `GET /country`

Get Country List

## Signature

```php
public function list(): array
```

## Returns

`list<Country>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->countries->list();
```
