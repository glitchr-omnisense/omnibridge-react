---
title: The JavaScript
order: 30
---

# The JavaScript

`js/src/index.js`, an ES module importing `react` (`createContext`, `createElement`, `useContext`,
`useEffect`, `useState`), `react-dom` (`flushSync`) and `react-dom/client` (`createRoot`); its types
in `js/src/index.d.ts`.

## createApp

```ts
createApp(options: {
  resolve: (name: string) => ComponentType<any> | undefined;   // the component of a page's name
  el?: HTMLElement | null;                                     // default document.getElementById('app')
  prefix?: string;                                             // default '/': the links followed within the front end
}): void
```

1. Reads the page from the root element's `data-page` (the shell's).
2. Replaces the history's current entry with `{omnibridge: page}` at the page's `url`.
3. Listens to clicks inside the root element: a plain `<a href>` of the page's own HTML (an article's
   text) is followed within the front end when [it may be](#which-links-are-followed).
4. Renders the page at once (`flushSync`), before the page's other deferred scripts run, so a site's
   own behaviours loaded after the front end (a drawer, a sticky header) find their elements in place.

The page's component is `resolve(page.component)`, rendered with `page.props`, inside a context that
`usePage()` reads. A name `resolve` does not know throws `Error('omnibridge/react: no component "<name>"')`
while rendering.

## Link

```jsx
import { Link } from '@omnibridge/react';

<Link href="/articles/le-fuji" className="card">Le Fuji</Link>
<Link href="/articles?page=2" replace preserveScroll>Next</Link>
```

An `<a>` with every attribute given; on a click that [may be followed](#which-links-are-followed) and
that the `onClick` given did not prevent, `visit(href, {replace, preserveScroll})` instead of the
browser's navigation.

## visit

```ts
visit(url: string, options?: { replace?: boolean; preserveScroll?: boolean }): Promise<Page>
```

Asks the page (`fetchPage()`), pushes it in the history at its `url` (or replaces the current entry),
shows it, then - unless `preserveScroll` - scrolls to the element of the URL's fragment, else to the top.

## fetchPage

```ts
fetchPage(url: string): Promise<Page>
```

A `fetch()` with `X-Omnibridge: true`, `X-Omnibridge-Version` (the version of the page shown, `''`
before any), `Accept: application/json`, the same-origin credentials. Without showing it: a feed's
next batch is a page whose props the component appends.

- 409: the browser loads `X-Omnibridge-Location` (or the URL) whole; the promise never settles.
- Not OK, or an answer without `X-Omnibridge` (an error page, another application's page): the
  browser loads the URL whole; the promise never settles.
- Otherwise the page's JSON.

## usePage

```ts
usePage<P>(): Page<P>   // { component, props, url, version }
```

The page shown, from any component under the page's (a layout's header reading the frame's props).

## Which links are followed

A click is followed within the front end when all of these hold; otherwise the browser has it:

- the main button, with no modifier key (Ctrl, Cmd, Shift, Alt);
- the anchor has no `target` other than `_self`, no `download`, and no `data-omnibridge="false"`;
- its URL is of the same origin and its path starts with `prefix`;
- it is not a link to a place of the current page (same path and query, with a fragment).

## Back and forward

Each page shown is kept in the history's state (`{omnibridge: page}`). On `popstate`, the state's page
is shown again as it was, without asking the server; an entry without one (made outside the front
end) is asked again with `visit(location.href, {replace: true})`.

## The types

```ts
export interface Page<P = Record<string, unknown>> {
  component: string;
  props: P;
  url: string;
  version: string | null;
}
```

`usePage`, `fetchPage` and `visit` are generic over the props.
