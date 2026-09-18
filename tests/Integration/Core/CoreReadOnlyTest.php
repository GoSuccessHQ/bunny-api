<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Integration\Core;

use DateTimeImmutable;
use GoSuccess\Bunny\Core\CoreClient;
use GoSuccess\Bunny\Core\Model\AuditLogEntry;
use GoSuccess\Bunny\Core\Model\BillingDetails;
use GoSuccess\Bunny\Core\Model\Country;
use GoSuccess\Bunny\Core\Model\DnsRecord;
use GoSuccess\Bunny\Core\Model\DnsZone;
use GoSuccess\Bunny\Core\Model\PullZone;
use GoSuccess\Bunny\Core\Model\Region;
use GoSuccess\Bunny\Core\Model\Statistics;
use GoSuccess\Bunny\Core\Model\StorageZone;
use GoSuccess\Bunny\Core\Model\VideoLibrary;
use GoSuccess\Bunny\Pagination\Page;
use GoSuccess\Bunny\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;

/**
 * Read-only checks of the Core Platform API against a real account.
 *
 * Only requests without side effects are made: GETs and the availability
 * checks, which use POST. `pullZones->loadFreeCertificate()` is deliberately
 * absent: despite being a GET it issues a certificate.
 */
#[CoversNothing]
final class CoreReadOnlyTest extends IntegrationTestCase
{
    private static ?CoreClient $client = null;

    public function testGeneralLists(): void
    {
        $countries = self::client()->countries->list();
        self::assertNotEmpty($countries);
        self::assertContainsOnlyInstancesOf(Country::class, $countries);

        $regions = self::client()->regions->list();
        self::assertNotEmpty($regions);
        self::assertContainsOnlyInstancesOf(Region::class, $regions);
    }

    public function testPullZones(): void
    {
        $client = self::client();
        $count = $client->pullZones->count();
        $page = $client->pullZones->list(page: 1, perPage: 5);

        self::assertSame($count, $page->totalItems);
        self::assertContainsOnlyInstancesOf(PullZone::class, $page->items);
        self::assertCount($count, iterator_to_array($client->pullZones->all(perPage: 5)));

        if ($page->items === []) {
            self::markTestSkipped('The account has no pull zones.');
        }

        $zone = $client->pullZones->get($page->items[0]->id);
        self::assertSame($page->items[0]->id, $zone->id);
        self::assertNotNull($zone->name);
        self::assertFalse($client->pullZones->checkAvailability($zone->name));
        self::assertTrue($client->pullZones->checkAvailability(self::unusedName()));

        $client->pullZones->optimizerStatistics($zone->id);
        $client->pullZones->originShieldQueueStatistics($zone->id);
        $client->pullZones->safeHopStatistics($zone->id);
    }

    public function testStorageZones(): void
    {
        $client = self::client();
        $page = $client->storageZones->list(perPage: 10);
        self::assertContainsOnlyInstancesOf(StorageZone::class, $page->items);

        if ($page->items === []) {
            self::markTestSkipped('The account has no storage zones.');
        }

        $zone = $client->storageZones->get($page->items[0]->id);
        self::assertSame($page->items[0]->name, $zone->name);
        self::assertNotNull($zone->name);
        self::assertFalse($client->storageZones->checkAvailability($zone->name));
        self::assertTrue($client->storageZones->checkAvailability(self::unusedName()));
        $client->storageZones->statistics($zone->id);
        $client->storageZones->egressStatistics($zone->id);
    }

    public function testDnsZonesAndRecords(): void
    {
        $client = self::client();
        $page = $client->dnsZones->list(perPage: 10);
        self::assertContainsOnlyInstancesOf(DnsZone::class, $page->items);

        if ($page->items === []) {
            self::markTestSkipped('The account has no DNS zones.');
        }

        $zone = $client->dnsZones->get($page->items[0]->id);
        self::assertSame($page->items[0]->domain, $zone->domain);
        self::assertNotNull($zone->domain);
        self::assertFalse($client->dnsZones->checkAvailability($zone->domain));
        self::assertTrue($client->dnsZones->checkAvailability(self::unusedName() . '.com'));

        $records = $client->dnsRecords->list($zone->id, perPage: 50);
        self::assertContainsOnlyInstancesOf(DnsRecord::class, $records->items);

        self::assertStringContainsString('IN', $client->dnsZones->export($zone->id));
        $client->dnsZones->statistics($zone->id);
    }

    public function testVideoLibraries(): void
    {
        $client = self::client();
        $page = $client->videoLibraries->list();
        self::assertContainsOnlyInstancesOf(VideoLibrary::class, $page->items);
        self::assertNotEmpty($client->videoLibraries->languages());

        if ($page->items === []) {
            self::markTestSkipped('The account has no video libraries.');
        }

        $library = $client->videoLibraries->get($page->items[0]->id);
        self::assertSame($page->items[0]->id, $library->id);
        $client->videoLibraries->transcribingStatistics($library->id);
        $client->videoLibraries->drmStatistics($library->id);
    }

    public function testStatisticsBillingAndAccount(): void
    {
        $client = self::client();

        self::assertInstanceOf(Statistics::class, $client->statistics->get(dateFrom: new DateTimeImmutable('-2 days'), hourly: true));
        self::assertInstanceOf(BillingDetails::class, $client->billing->details());
        $client->billing->summary();
        $client->billing->paymentRequests();
        self::assertInstanceOf(Page::class, $client->apiKeys->list());

        foreach ($client->auditLog->list(new DateTimeImmutable('-1 day'), limit: 5)->items as $entry) {
            self::assertInstanceOf(AuditLogEntry::class, $entry);
        }

        $client->search->query('a', size: 5);
        $client->loadBalancers->accountStatistics();
    }

    public function testBillingSummaryPdf(): void
    {
        $records = self::client()->billing->details()->billingRecords;

        if ($records === []) {
            self::markTestSkipped('The account has no billing records.');
        }

        self::assertStringStartsWith('%PDF', self::client()->billing->summaryPdf($records[0]->id));
    }

    /**
     * A zone name nobody uses.
     */
    private static function unusedName(): string
    {
        $suffix = bin2hex(random_bytes(6));

        return "gosuccess-check-{$suffix}";
    }

    private static function client(): CoreClient
    {
        return self::$client ??= new CoreClient(self::apiKey());
    }
}
