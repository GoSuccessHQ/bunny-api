<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Core\Resource\Handwritten;

use GoSuccess\Bunny\Core\Model\DnsZoneImportResult;
use GoSuccess\Bunny\Core\Resource\DnsZoneResource;
use GoSuccess\Bunny\Http\Connection;
use GoSuccess\Bunny\Http\Method;
use GoSuccess\Bunny\Http\Stream;

/**
 * Hand-written methods of {@see DnsZoneResource}.
 */
trait DnsZoneOperations
{
    /**
     * Export the records of a zone as a BIND zone file.
     *
     * Bunny-specific records (pull zone, redirect, script) are not exported.
     *
     * `GET /dnszone/{id}/export`
     *
     * @param int $id The ID of the DNS zone
     *
     * @return string The zone file.
     */
    public function export(int $id): string
    {
        return $this->connection->send(Method::Get, "dnszone/{$id}/export", headers: ['Accept' => 'text/plain, */*'])->body;
    }

    /**
     * Import records from a BIND zone file.
     *
     * The file is sent as plain text, as bunny.net's own CLI does; the
     * specification does not document the request body. Record types other
     * than A, AAAA, CNAME, MX, TXT, SRV, CAA and PTR are skipped.
     *
     * `POST /dnszone/{zoneId}/import`
     *
     * @param int           $zoneId   The ID of the DNS zone
     * @param string|Stream $zoneFile The zone file contents.
     */
    public function import(int $zoneId, string|Stream $zoneFile): DnsZoneImportResult
    {
        $response = $this->connection->send(
            Method::Post,
            "dnszone/{$zoneId}/import",
            body: $zoneFile instanceof Stream ? $zoneFile->contents() : $zoneFile,
            headers: ['Content-Type' => 'text/plain'],
        );

        return self::toModel(DnsZoneImportResult::class, Connection::decodeJson($response->body));
    }

    /**
     * Check whether a zone name is still available.
     *
     * `POST /dnszone/checkavailability`
     *
     * @param string $name The zone name to check.
     */
    public function checkAvailability(string $name): bool
    {
        return Availability::read($this->connection->json(Method::Post, 'dnszone/checkavailability', body: ['Name' => $name]));
    }
}
