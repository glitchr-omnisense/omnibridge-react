<?php

namespace Omnibridge\React;

/**
 * How a page travels. The first visit is an HTML shell holding the page
 * (data-page on the root element), whose script renders it; a visit within
 * the site asks with the header X-Omnibridge and gets the page in JSON, its
 * version in X-Omnibridge-Version; a front end of another version than the
 * server's reloads the address whole (409 and X-Omnibridge-Location).
 * Framework-free: an application turns these answers into its responses.
 */
final class Protocol
{
    public const HEADER = 'X-Omnibridge';
    public const VERSION = 'X-Omnibridge-Version';
    public const LOCATION = 'X-Omnibridge-Location';

    /** A visit within the site: the page in JSON. */
    public static function wantsJson(?string $header): bool
    {
        return null !== $header && '' !== $header && 'false' !== strtolower($header);
    }

    /**
     * The answer to a request, as status, headers and body; the shell is the
     * application's (its head, its scripts) around the root element given.
     *
     * @param \Closure(string $root): string $shell
     *
     * @return array{status: int, headers: array<string, string>, body: string}
     */
    public static function answer(Page $page, ?string $header, ?string $clientVersion, \Closure $shell): array
    {
        if (self::wantsJson($header)) {
            if (null !== $page->version && null !== $clientVersion && '' !== $clientVersion && $clientVersion !== $page->version) {
                return ['status' => 409, 'headers' => [self::LOCATION => $page->url, 'Vary' => self::HEADER], 'body' => ''];
            }

            return [
                'status' => 200,
                'headers' => ['Content-Type' => 'application/json', self::HEADER => 'true', 'Vary' => self::HEADER] + (null !== $page->version ? [self::VERSION => $page->version] : []),
                'body' => self::json($page),
            ];
        }

        return ['status' => 200, 'headers' => ['Content-Type' => 'text/html; charset=UTF-8', 'Vary' => self::HEADER], 'body' => $shell(self::root($page))];
    }

    public static function json(Page $page): string
    {
        return json_encode($page, \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES | \JSON_THROW_ON_ERROR);
    }

    /** The root element holding the page, for the shell's body. */
    public static function root(Page $page, string $id = 'app'): string
    {
        return '<div id="'.htmlspecialchars($id, \ENT_QUOTES).'" data-page="'.htmlspecialchars(self::json($page), \ENT_QUOTES | \ENT_SUBSTITUTE).'"></div>';
    }
}
