<?php

namespace Omnibridge\React;

/**
 * A page as React renders it: the name of its component, its props (plain
 * data), its address and the version of the front end that renders it. The
 * same from any framework: Symfony and Laravel give it alike.
 */
final class Page implements \JsonSerializable
{
    /** @param array<string, mixed> $props */
    public function __construct(
        public readonly string $component,
        public readonly array $props,
        public readonly string $url,
        public readonly ?string $version = null,
    ) {
    }

    /** @return array{component: string, props: array<string, mixed>, url: string, version: ?string} */
    public function jsonSerialize(): array
    {
        return ['component' => $this->component, 'props' => $this->props, 'url' => $this->url, 'version' => $this->version];
    }
}
