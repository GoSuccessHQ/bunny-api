<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Storage\Model;

use DateTimeImmutable;
use GoSuccess\Bunny\Model\Cast;
use GoSuccess\Bunny\Model\ResponseModel;

/**
 * A file or directory in an Edge Storage zone.
 *
 * Besides the documented fields, the API returns the SHA-256 checksum, the
 * replication regions and the content type, which are included here.
 */
final class StorageObject implements ResponseModel
{
    /**
     * The path relative to the zone root, as the storage methods take it:
     * `images/logo.png`, or `images/` for a directory.
     */
    public string $relativePath {
        get {
            $directory = ltrim($this->path, '/');
            $zonePrefix = "{$this->storageZoneName}/";

            if ($this->storageZoneName !== '' && str_starts_with($directory, $zonePrefix)) {
                $directory = substr($directory, \strlen($zonePrefix));
            }

            return $directory . $this->objectName . ($this->isDirectory ? '/' : '');
        }
    }

    /**
     * @param string                 $guid            The unique ID of the object.
     * @param string                 $storageZoneName The name of the storage zone.
     * @param string                 $path            The directory, including the zone name, e.g. `/my-zone/images/`.
     * @param string                 $objectName      The file or directory name.
     * @param int                    $length          The size in bytes (0 for directories).
     * @param DateTimeImmutable|null $lastChanged     When the object was last changed (UTC).
     * @param bool                   $isDirectory     Whether the object is a directory.
     * @param int                    $serverId        The ID of the server holding the object.
     * @param int                    $arrayNumber     The storage array holding the object.
     * @param string|null            $userId          The ID of the account owning the object.
     * @param string|null            $contentType     The content type it is served with; empty when derived from the extension.
     * @param DateTimeImmutable|null $dateCreated     When the object was created (UTC).
     * @param int                    $storageZoneId   The ID of the storage zone.
     * @param string|null            $checksum        The SHA-256 checksum of the file (upper-case hex).
     * @param list<string>           $replicatedZones The region codes the file is replicated to, e.g. `SE`.
     */
    public function __construct(
        public readonly string $guid = '',
        public readonly string $storageZoneName = '',
        public readonly string $path = '',
        public readonly string $objectName = '',
        public readonly int $length = 0,
        public readonly ?DateTimeImmutable $lastChanged = null,
        public readonly bool $isDirectory = false,
        public readonly int $serverId = 0,
        public readonly int $arrayNumber = 0,
        public readonly ?string $userId = null,
        public readonly ?string $contentType = null,
        public readonly ?DateTimeImmutable $dateCreated = null,
        public readonly int $storageZoneId = 0,
        public readonly ?string $checksum = null,
        public readonly array $replicatedZones = [],
    ) {}

    public static function fromArray(array $data): static
    {
        $replicated = Cast::string($data['ReplicatedZones'] ?? null) ?? '';

        return new self(
            guid: Cast::string($data['Guid'] ?? null) ?? '',
            storageZoneName: Cast::string($data['StorageZoneName'] ?? null) ?? '',
            path: Cast::string($data['Path'] ?? null) ?? '',
            objectName: Cast::string($data['ObjectName'] ?? null) ?? '',
            length: Cast::int($data['Length'] ?? null) ?? 0,
            lastChanged: Cast::dateTime($data['LastChanged'] ?? null),
            isDirectory: Cast::bool($data['IsDirectory'] ?? null) ?? false,
            serverId: Cast::int($data['ServerId'] ?? null) ?? 0,
            arrayNumber: Cast::int($data['ArrayNumber'] ?? null) ?? 0,
            userId: Cast::string($data['UserId'] ?? null),
            contentType: Cast::string($data['ContentType'] ?? null),
            dateCreated: Cast::dateTime($data['DateCreated'] ?? null),
            storageZoneId: Cast::int($data['StorageZoneId'] ?? null) ?? 0,
            checksum: Cast::string($data['Checksum'] ?? null),
            // A comma-separated list, as bunny.net's own CLI parses it.
            replicatedZones: array_values(array_filter(array_map(trim(...), explode(',', $replicated)), static fn(string $zone): bool => $zone !== '')),
        );
    }
}
