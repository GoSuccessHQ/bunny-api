<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Unit\Core;

use GoSuccess\Bunny\Core\CoreClient;
use GoSuccess\Bunny\Core\Pagination;
use GoSuccess\Bunny\Core\Resource\Handwritten\Availability;
use GoSuccess\Bunny\Exception\SerializationException;
use GoSuccess\Bunny\Http\Method;
use GoSuccess\Bunny\Http\Response;
use GoSuccess\Bunny\Http\Stream;
use GoSuccess\Bunny\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Pagination::class)]
#[CoversClass(Availability::class)]
final class HandwrittenOperationsTest extends TestCase
{
    public function testSetEnabledSendsThePullZoneIdAsBodyId(): void
    {
        $http = new MockHttpClient(new Response(204));

        $this->client($http)->edgeRules->setEnabled(42, 'a1b2-c3', false);

        self::assertSame(Method::Post, $http->requests[0]->method);
        self::assertSame('https://api.bunny.net/pullzone/42/edgerules/a1b2-c3/setEdgeRuleEnabled', $http->requests[0]->uri);
        self::assertSame(['Id' => 42, 'Value' => false], $http->jsonBody());
    }

    public function testCheckAvailabilityReadsTheAvailableField(): void
    {
        $http = new MockHttpClient(new Response(200, '{"Available":true}'), new Response(200, '{"Available":false}'), new Response(200, '{"Available":true}'));
        $client = $this->client($http);

        self::assertTrue($client->pullZones->checkAvailability('zone'));
        self::assertFalse($client->storageZones->checkAvailability('zone'));
        self::assertTrue($client->dnsZones->checkAvailability('example.com'));

        self::assertSame('https://api.bunny.net/pullzone/checkavailability', $http->requests[0]->uri);
        self::assertSame('https://api.bunny.net/storagezone/checkavailability', $http->requests[1]->uri);
        self::assertSame('https://api.bunny.net/dnszone/checkavailability', $http->requests[2]->uri);
        self::assertSame(['Name' => 'zone'], $http->jsonBody(0));
    }

    public function testCheckAvailabilityFailsLoudlyOnAnUnknownAnswer(): void
    {
        $this->expectException(SerializationException::class);

        $this->client(new MockHttpClient(new Response(200, '{"Something":1}')))->pullZones->checkAvailability('zone');
    }

    public function testExportReturnsTheZoneFile(): void
    {
        $http = new MockHttpClient(new Response(200, "example.com. IN 300 A 1.2.3.4\n"));

        self::assertSame("example.com. IN 300 A 1.2.3.4\n", $this->client($http)->dnsZones->export(7));
        self::assertSame('https://api.bunny.net/dnszone/7/export', $http->requests[0]->uri);
    }

    public function testImportSendsTheZoneFileAsPlainText(): void
    {
        $http = new MockHttpClient(new Response(200, '{"RecordsSuccessful":2,"RecordsFailed":0,"RecordsSkipped":1}'));

        $result = $this->client($http)->dnsZones->import(7, Stream::fromString('zone file'));

        self::assertSame(2, $result->recordsSuccessful);
        self::assertSame(1, $result->recordsSkipped);
        self::assertSame('zone file', $http->bodies[0]);
        self::assertSame('text/plain', $http->requests[0]->headers['Content-Type']);
    }

    public function testImageUploadsSendTheRawImage(): void
    {
        $http = new MockHttpClient(new Response(204), new Response(204), new Response(204));
        $libraries = $this->client($http)->videoLibraries;

        $libraries->addWatermark(3, 'png-bytes');
        $libraries->addLiveThumbnail(3, 'jpeg-bytes', 'image/jpeg');
        $libraries->addLiveWatermark(3, Stream::fromString('png-bytes'));

        self::assertSame(Method::Put, $http->requests[0]->method);
        self::assertSame('https://api.bunny.net/videolibrary/3/watermark', $http->requests[0]->uri);
        self::assertSame('https://api.bunny.net/videolibrary/3/live/thumbnail', $http->requests[1]->uri);
        self::assertSame('https://api.bunny.net/videolibrary/3/live/watermark', $http->requests[2]->uri);
        self::assertSame('image/jpeg', $http->requests[1]->headers['Content-Type']);
        self::assertSame(['png-bytes', 'jpeg-bytes', 'png-bytes'], $http->bodies);
    }

    public function testPdfDownloadsReturnTheDocument(): void
    {
        $http = new MockHttpClient(new Response(200, '%PDF-1.4'), new Response(200, '%PDF-1.7'));
        $billing = $this->client($http)->billing;

        self::assertSame('%PDF-1.4', $billing->summaryPdf(11));
        self::assertSame('%PDF-1.7', $billing->paymentRequestInvoicePdf(12));
        self::assertSame('https://api.bunny.net/billing/summary/11/pdf', $http->requests[0]->uri);
        self::assertSame('https://api.bunny.net/billing/payment-request-invoice/12/pdf', $http->requests[1]->uri);
        self::assertSame('application/pdf', $http->requests[0]->headers['Accept']);
    }

    public function testPagePaginationAdvancesWhileMoreItemsFollow(): void
    {
        $page = Pagination::page(['CurrentPage' => 2, 'TotalItems' => 30, 'HasMoreItems' => true], ['a']);

        self::assertSame(2, $page->currentPage);
        self::assertSame(30, $page->totalItems);
        self::assertSame(3, $page->next);

        self::assertNull(Pagination::page(['CurrentPage' => 3, 'HasMoreItems' => false], [])->next);
    }

    public function testContinuationPaginationStopsOnAnEmptyToken(): void
    {
        self::assertSame('abc', Pagination::continuation(['HasMoreData' => true, 'ContinuationToken' => 'abc'], ['a'])->next);
        self::assertNull(Pagination::continuation(['HasMoreData' => false, 'ContinuationToken' => ''], ['a'])->next);
        self::assertNull(Pagination::continuation(['HasMoreData' => true, 'ContinuationToken' => ''], ['a'])->next);
    }

    private function client(MockHttpClient $http): CoreClient
    {
        return new CoreClient('secret', httpClient: $http);
    }
}
