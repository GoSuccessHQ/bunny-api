# `$bunny->core->regions->list()`

> Core Platform API · `GET /region`

Region list

## Signature

```php
public function list(): array
```

## Returns

`list<Region>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->regions->list();
```
