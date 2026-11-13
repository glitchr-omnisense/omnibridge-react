# omnibridge/react

A page of a site as a React component and its props, the same from any
framework (plan 0, rule 13). Symfony and Laravel give the page alike; React
renders it.

- **PHP** (`Omnibridge\React`, framework-free): `Page` (component, props,
  url, version) and `Protocol::answer()`. The first visit is an HTML shell
  holding the page (`data-page` on the root element); a visit within the
  site (header `X-Omnibridge`) gets the page in JSON; a front end of another
  version reloads (409, `X-Omnibridge-Location`). The application turns the
  answer into its own response, its shell around the root element.
- **JavaScript** (`js/`, `@omnibridge/react`, plain ES modules): `createApp()`
  renders the page of the shell, `Link` and the links of the page under the
  prefix are followed without reloading (`visit()`), back and forward restore
  the pages seen, `fetchPage()` asks a page without showing it (a feed's next
  batch), `usePage()` reads the page shown.

## Install

```sh
composer require omnibridge/react
```

PHP 8.2 or later, nothing else. The JavaScript is installed from this
repository, as Composer installs the PHP: the package.json at its root is
`@omnibridge/react` (its files under `js/src`, React and React DOM 18 or later
as peer dependencies), at a tag or a commit. It is not published on npm.

```json
"dependencies": { "@omnibridge/react": "github:glitchr-omnisense/omnibridge-react#v1.0.0" }
```

## First use

```php
use Omnibridge\React\Page;
use Omnibridge\React\Protocol;

$page = new Page('Article', ['title' => 'Hello'], '/articles/hello', 'build-42');
$shell = static fn (string $root): string => "<!doctype html><html><body>$root<script type=\"module\" src=\"/build/app.js\"></script></body></html>";

// The request's X-Omnibridge and X-Omnibridge-Version headers (null on a first visit).
$answer = Protocol::answer($page, $_SERVER['HTTP_X_OMNIBRIDGE'] ?? null, $_SERVER['HTTP_X_OMNIBRIDGE_VERSION'] ?? null, $shell);
// ['status' => 200, 'headers' => [...], 'body' => '<!doctype html>... data-page="..." ...']: your framework's response.
```

```js
// assets/app.js
import { createApp } from '@omnibridge/react';
import Article from './pages/Article.jsx';

createApp({ resolve: (name) => ({ Article })[name] });
```

The [manual](docs/index.md) has the protocol, the JavaScript and the answer in
a Symfony or Laravel response.

lesringostars uses it for its blog and its articles, given by its Symfony site
and by its Laravel copy from the same screens (lesringostars-react).

## Tests

```bash
docker run --rm -v "$PWD":/app -w /app ghcr.io/glitchr-studio/php:8.4 sh -c "composer update --no-progress && vendor/bin/phpunit tests"
```
