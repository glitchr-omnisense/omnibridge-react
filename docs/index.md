# omnibridge/react

A page of a site as a React component and its props, the same from any framework (plan 0, rule 13).
Symfony and Laravel give the page alike; React renders it.

- **PHP** (`Omnibridge\React`, framework-free): `Page` (component, props, url, version) and
  `Protocol::answer()`. The first visit is an HTML shell holding the page (`data-page` on the root
  element); a visit within the site (header `X-Omnibridge`) gets the page in JSON; a front end of
  another version reloads (409, `X-Omnibridge-Location`). The application turns the answer into its
  own response, its shell around the root element.
- **JavaScript** (`js/`, `@omnibridge/react`, plain ES modules): `createApp()` renders the page of the
  shell, `Link` and the links of the page under the prefix are followed without reloading (`visit()`),
  back and forward restore the pages seen, `fetchPage()` asks a page without showing it (a feed's next
  batch), `usePage()` reads the page shown.

The JavaScript is installed from this repository, as Composer installs the PHP: the package.json at
its root is `@omnibridge/react` (its files under `js/src`), at a commit or a tag of the repository -

```json
"dependencies": { "@omnibridge/react": "github:glitchr-omnisense/omnibridge-react#<commit>" }
```

lesringostars uses it for its blog and its articles, given by its Symfony site and by its Laravel copy
from the same screens (lesringostars-react). omnisense/omnibase's `Base\Screen\Negotiation` answers
its screens with this protocol when the package is installed, under Symfony
(`Base\Bridge\Symfony\ScreenResponder`) and under Laravel (omnibridge/laravel's `Screen\Responder`).

## How this manual is organised

- [Installation](installation.md): the PHP package, the JavaScript package, a page answered three
  ways (run, with its output).
- [The protocol](protocol.md): `Page`, `Protocol`, every header and status, the answer in a Symfony
  or Laravel response.
- [The JavaScript](javascript.md): `createApp()`, `Link`, `visit()`, `fetchPage()`, `usePage()`, which
  links are followed, the history.
- [Testing](testing.md), [Verified, and not](verified.md).

## Licence

MIT.
