<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tools\Generator\Docs;

/**
 * A class whose public methods are documented: a resource, or a client that
 * offers its operations directly.
 */
final readonly class DocTarget
{
    /**
     * @param string|null       $property    Property of the client holding the resource, or null for
     *                                       methods of the client itself.
     * @param string            $class       Fully qualified class name.
     * @param list<string>|null $methods     Method names in documentation order; null documents every
     *                                       public method the class declares, in declaration order.
     */
    public function __construct(
        public ?string $property,
        public string $class,
        public string $description,
        public ?array $methods = null,
    ) {}
}
