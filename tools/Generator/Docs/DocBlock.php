<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tools\Generator\Docs;

/**
 * The parts of a method docblock the reference pages need.
 */
final readonly class DocBlock
{
    /**
     * @param list<string>                                           $paragraphs
     * @param array<string, array{type: string, description: string}> $params
     */
    public function __construct(
        public string $summary,
        public array $paragraphs,
        public ?string $endpoint,
        public array $params,
        public ?string $return,
        public bool $deprecated,
    ) {}

    public static function parse(string $comment): self
    {
        $lines = [];

        foreach (explode("\n", $comment) as $line) {
            // Strip the comment markers: a leading "/**" or "*", a trailing "*/".
            $line = (string) preg_replace(['~^\s*(/\*\*|\*/|\*)\s?~', '~\s*\*/$~'], '', rtrim($line));
            $lines[] = rtrim($line);
        }

        $text = [];
        $tags = [];
        $current = null;

        foreach ($lines as $line) {
            if (str_starts_with(ltrim($line), '@')) {
                $tags[] = ltrim($line);
                $current = \count($tags) - 1;

                continue;
            }

            if ($current !== null && $line !== '') {
                $tags[$current] .= ' ' . trim($line);

                continue;
            }

            $current = null;
            $text[] = $line;
        }

        $paragraphs = array_values(array_filter(
            array_map(static fn(string $paragraph): string => trim((string) preg_replace('/\s*\n\s*/', ' ', $paragraph)), explode("\n\n", implode("\n", $text))),
            static fn(string $paragraph): bool => $paragraph !== '',
        ));

        $endpoint = null;

        foreach ($paragraphs as $index => $paragraph) {
            if (preg_match('~^`((?:GET|POST|PUT|PATCH|DELETE) [^`]+)`$~', $paragraph, $match)) {
                $endpoint = $match[1];
                unset($paragraphs[$index]);
            }
        }

        $paragraphs = array_values($paragraphs);
        $summary = array_shift($paragraphs) ?? '';
        $params = [];
        $return = null;
        $deprecated = false;

        foreach ($tags as $tag) {
            if (preg_match('/^@param\s+(\S+)\s+\$(\w+)\s*(.*)$/s', $tag, $match)) {
                $params[$match[2]] = ['type' => $match[1], 'description' => trim($match[3])];
            } elseif (preg_match('/^@return\s+(\S+)/', $tag, $match)) {
                $return = $match[1];
            } elseif (str_starts_with($tag, '@deprecated')) {
                $deprecated = true;
            }
        }

        return new self($summary, $paragraphs, $endpoint, $params, $return, $deprecated);
    }
}
