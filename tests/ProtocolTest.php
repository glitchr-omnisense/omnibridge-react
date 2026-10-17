<?php

namespace Omnibridge\React\Tests;

use Omnibridge\React\Page;
use Omnibridge\React\Protocol;
use PHPUnit\Framework\TestCase;

final class ProtocolTest extends TestCase
{
    private function page(): Page
    {
        return new Page('Blog', ['destination' => ['title' => 'Japon - 日本'], 'html' => '<p>"é"</p>'], '/react/japon/articles', 'v1');
    }

    public function testTheFirstVisitIsTheShellHoldingThePage(): void
    {
        $answer = Protocol::answer($this->page(), null, null, static fn (string $root) => '<body>'.$root.'</body>');

        self::assertSame(200, $answer['status']);
        self::assertSame('text/html; charset=UTF-8', $answer['headers']['Content-Type']);
        self::assertSame(1, preg_match('~<div id="app" data-page="([^"]+)"></div>~', $answer['body'], $match));
        self::assertSame($this->page()->jsonSerialize(), json_decode(html_entity_decode($match[1], \ENT_QUOTES), true));
    }

    public function testAVisitWithinTheSiteIsThePageInJson(): void
    {
        $answer = Protocol::answer($this->page(), 'true', 'v1', static fn () => self::fail('no shell'));

        self::assertSame('application/json', $answer['headers']['Content-Type']);
        self::assertSame('v1', $answer['headers']['X-Omnibridge-Version']);
        self::assertSame('X-Omnibridge', $answer['headers']['Vary']);
        self::assertSame('Blog', json_decode($answer['body'], true)['component']);
        self::assertStringContainsString('Japon - 日本', $answer['body'], 'unicode as it is');
    }

    public function testAFrontEndOfAnotherVersionReloads(): void
    {
        $answer = Protocol::answer($this->page(), 'true', 'v0', static fn () => '');

        self::assertSame(409, $answer['status']);
        self::assertSame('/react/japon/articles', $answer['headers']['X-Omnibridge-Location']);
    }
}
