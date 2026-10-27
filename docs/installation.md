---
title: Installation
order: 10
---

# Installation

## The PHP package

```sh
composer require omnibridge/react
```

PHP 8.2 or later; nothing else (no framework, no PSR interface).

## The JavaScript package

```json
{
  "dependencies": {
    "@omnibridge/react": "github:glitchr-omnisense/omnibridge-react#<commit>",
    "react": "^19.0.0",
    "react-dom": "^19.0.0"
  }
}
```

`package.json` at the repository's root: `@omnibridge/react` 0.1.0, an ES module (`"type": "module"`),
`main` and `exports` at `js/src/index.js`, the types at `js/src/index.d.ts`, the files `js/src` only.
React and React DOM 18 or later are peer dependencies: the application installs them. No build step:
the bundler of the application (webpack Encore, Vite...) takes the module as it is.

## A page answered three ways

```php
<?php

require 'vendor/autoload.php';

use Omnibridge\React\Page;
use Omnibridge\React\Protocol;

$page = new Page('Article', ['title' => 'Le Fuji « 富士山 »'], '/articles/le-fuji', 'build-42');
$shell = static fn (string $root): string => "<!doctype html><html><body>$root<script type=\"module\" src=\"/build/app.js\"></script></body></html>";

foreach ([[null, null], ['true', 'build-42'], ['true', 'build-41']] as [$header, $version]) {
    $answer = Protocol::answer($page, $header, $version, $shell);
    echo 'X-Omnibridge: ', $header ?? '(none)', ', X-Omnibridge-Version: ', $version ?? '(none)', "\n";
    echo '  ', $answer['status'], ' ', json_encode($answer['headers'], JSON_UNESCAPED_SLASHES), "\n";
    echo '  ', $answer['body'], "\n";
}
```

Its output (PHP 8.4.27, 2026-10-27):

```text
X-Omnibridge: (none), X-Omnibridge-Version: (none)
  200 {"Content-Type":"text/html; charset=UTF-8","Vary":"X-Omnibridge"}
  <!doctype html><html><body><div id="app" data-page="{&quot;component&quot;:&quot;Article&quot;,&quot;props&quot;:{&quot;title&quot;:&quot;Le Fuji « 富士山 »&quot;},&quot;url&quot;:&quot;/articles/le-fuji&quot;,&quot;version&quot;:&quot;build-42&quot;}"></div><script type="module" src="/build/app.js"></script></body></html>
X-Omnibridge: true, X-Omnibridge-Version: build-42
  200 {"Content-Type":"application/json","X-Omnibridge":"true","Vary":"X-Omnibridge","X-Omnibridge-Version":"build-42"}
  {"component":"Article","props":{"title":"Le Fuji « 富士山 »"},"url":"/articles/le-fuji","version":"build-42"}
X-Omnibridge: true, X-Omnibridge-Version: build-41
  409 {"X-Omnibridge-Location":"/articles/le-fuji","Vary":"X-Omnibridge"}
```

- A first visit (no header): the application's shell around the root element, the page in its
  `data-page` attribute.
- A visit within the site, same version: the page in JSON.
- A front end built for another version: 409, the address to reload whole, no body.

And the front end that renders it:

```js
// assets/app.js
import { createApp } from '@omnibridge/react';
import Article from './pages/Article.jsx';
import Blog from './pages/Blog.jsx';

const pages = { Article, Blog };
createApp({ resolve: (name) => pages[name] });
```
