# `$bunny->shield->ddos->enums()`

> Shield API · `GET /shield/ddos/enums`

List of all DDoS Enum Mappings

## Signature

```php
public function enums(): array
```

## Returns

`list<WafEnum>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->ddos->enums();
```
