# Examples

Runnable scripts that show the client in use. **They only read data**, so they
are safe to run against a production account; write operations are shown in
the [README](../README.md) and the [API reference](../docs/README.md).

```bash
composer install
BUNNY_API_KEY=your-api-key php examples/core-pull-zones.php
```

| Script | Shows |
| --- | --- |
| [core-pull-zones.php](core-pull-zones.php) | Iterating over all pull zones with hostnames and edge rules |
| [core-dns.php](core-dns.php) | DNS zones, their records and the zone file export |
| [core-statistics.php](core-statistics.php) | Traffic statistics with date filters and chart data |
| [origin-errors.php](origin-errors.php) | Yesterday's origin errors of all pull zones |
| [logging.php](logging.php) | Recent failed requests from the CDN logs |
| [storage.php](storage.php) | Browsing a storage zone with its read-only password |
| [stream.php](stream.php) | Videos, collections and views of a video library with its read-only key |
| [shield.php](shield.php) | Shield zones with plan, WAF mode, rate limits and yesterday's event logs |
| [edge-scripting.php](edge-scripting.php) | Edge scripts with type, hostname, linked pull zones and active release |
| [error-handling.php](error-handling.php) | Typed exceptions and the details they carry |
