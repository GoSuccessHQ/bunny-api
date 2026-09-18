<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Logging\Model;

use Generator;
use GoSuccess\Bunny\Http\Stream;
use IteratorAggregate;

/**
 * A log file from the legacy (v1) endpoint.
 *
 * The file lives in a temporary stream; its lines are parsed only while you
 * iterate, so large logs use little memory. Iterating again starts over. To
 * keep the raw file, copy {@see $stream}, e.g. with stream_copy_to_stream().
 *
 * @implements IteratorAggregate<int, LegacyLogEntry>
 */
final readonly class LegacyLog implements IteratorAggregate
{
    /**
     * @param Stream $stream The raw log file, one request per line.
     */
    public function __construct(public Stream $stream) {}

    /**
     * @return Generator<int, LegacyLogEntry>
     */
    public function getIterator(): Generator
    {
        rewind($this->stream->resource);
        $index = 0;

        while (($line = fgets($this->stream->resource)) !== false) {
            if (trim($line) !== '') {
                yield $index++ => LegacyLogEntry::fromLine($line);
            }
        }
    }
}
