---
title: The protocol
order: 20
---

# The protocol

## Page

```php
final class Page implements \JsonSerializable
{
    public function __construct(
        public readonly string $component,   // the React component's name, which the front end resolves
        public readonly array $props,        // plain data: the component's props
        public readonly string $url,         // the page's address, put in the browser's history
        public readonly ?string $version = null,   // the front end's version (a build's hash), null for none
    ) {}
}
```

`jsonSerialize()` is `{component, props, url, version}`, in that order.

## Protocol

| Constant | Value | Meaning |
|---|---|---|
| `HEADER` | `X-Omnibridge` | sent by the front end on a visit within the site; answered `true` on a JSON page |
| `VERSION` | `X-Omnibridge-Version` | sent by the front end with the version it was built as; answered with the page's |
| `LOCATION` | `X-Omnibridge-Location` | on a 409: the address to load whole |

| Static method | Returns |
|---|---|
| `wantsJson(?string $header): bool` | true for any value of `X-Omnibridge` but none, `''` and `false` (any case) |
| `answer(Page $page, ?string $header, ?string $clientVersion, \Closure $shell): array` | `{status, headers, body}`, as below |
| `json(Page $page): string` | the page in JSON, Unicode and slashes unescaped; `\JsonException` for props JSON cannot encode |
| `root(Page $page, string $id = 'app'): string` | `<div id="app" data-page="..."></div>`, the JSON escaped for an attribute |

`answer()`:

| Request | Status | Headers | Body |
|---|---|---|---|
| no `X-Omnibridge` (a first visit, a reload) | 200 | `Content-Type: text/html; charset=UTF-8`, `Vary: X-Omnibridge` | `$shell(Protocol::root($page))`: the application's HTML around the root element |
| `X-Omnibridge`, and the page has no version, or the client sent none, or the same | 200 | `Content-Type: application/json`, `X-Omnibridge: true`, `Vary: X-Omnibridge`, `X-Omnibridge-Version` (when the page has a version) | `Protocol::json($page)` |
| `X-Omnibridge` and another version than the page's | 409 | `X-Omnibridge-Location: <page url>`, `Vary: X-Omnibridge` | `''` |

`Vary: X-Omnibridge` on every answer: a cache keeps the shell and the JSON of one address apart.
The protocol says nothing of caching otherwise, nor of a visitor's privacy: omnibase's
`Base\Screen\Negotiation` adds `Vary: Cookie` and `private` for a page that carries a visitor.

## The answer in a response

Under Symfony:

```php
use Omnibridge\React\Page;
use Omnibridge\React\Protocol;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

public function article(Request $request, string $slug): Response
{
    $page = new Page('Article', ['slug' => $slug, 'title' => '...'], $request->getRequestUri(), $this->version);
    $answer = Protocol::answer(
        $page,
        $request->headers->get(Protocol::HEADER),
        $request->headers->get(Protocol::VERSION),
        fn (string $root) => $this->renderView('react.html.twig', ['root' => $root]),
    );

    return new Response($answer['body'], $answer['status'], $answer['headers']);
}
```

```twig
{# templates/react.html.twig #}
<!doctype html>
<html>
<head>{{ encore_entry_link_tags('app') }}{{ encore_entry_script_tags('app') }}</head>
<body>{{ root|raw }}</body>
</html>
```

Under Laravel, the same with `$request->header(Protocol::HEADER)` and
`response($answer['body'], $answer['status'], $answer['headers'])`. The version is the application's
choice: a hash of its build's manifest, changed at each deployment, so a front end left open in a tab
reloads the next page whole instead of rendering it with stale components. These two snippets show
the wiring; they were not run here (the page's own script was, [Installation](installation.md)).
