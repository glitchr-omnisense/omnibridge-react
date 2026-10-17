// omnibridge/react, the client: the page a server gives (its component, its
// props, its address, its version) rendered by React, and the site's links
// followed without reloading - the next page asked in JSON (X-Omnibridge),
// the history kept, back and forward restoring the pages seen.
import { createContext, createElement, useContext, useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';

const PageContext = createContext(null);
let navigate = null;
let current = null;
let options = { prefix: '/', resolve: null };

/** The page shown: { component, props, url, version }. */
export function usePage() {
  return useContext(PageContext);
}

/** Ask a page of the server in JSON, without showing it (a feed's next batch). */
export async function fetchPage(url) {
  const response = await fetch(url, {
    headers: { 'X-Omnibridge': 'true', 'X-Omnibridge-Version': current?.version ?? '', Accept: 'application/json' },
    credentials: 'same-origin',
  });
  if (409 === response.status) {
    window.location.href = response.headers.get('X-Omnibridge-Location') || url;
    return new Promise(() => {});
  }
  if (!response.ok || !response.headers.get('X-Omnibridge')) {
    // Not a page of the front end (an error, another site's page): the browser shows it.
    window.location.href = url;
    return new Promise(() => {});
  }
  return response.json();
}

/** Go to a page of the site: asked in JSON, shown, kept in the history. */
export async function visit(url, { replace = false, preserveScroll = false } = {}) {
  const page = await fetchPage(url);
  window.history[replace ? 'replaceState' : 'pushState']({ omnibridge: page }, '', page.url);
  navigate?.(page);
  if (!preserveScroll) {
    const hash = new URL(page.url, window.location.href).hash;
    const target = hash ? document.getElementById(decodeURIComponent(hash.slice(1))) : null;
    target ? target.scrollIntoView() : window.scrollTo(0, 0);
  }
  return page;
}

/** A link of the site, followed without reloading. */
export function Link({ href, replace, preserveScroll, onClick, ...rest }) {
  return createElement('a', {
    href,
    ...rest,
    onClick(event) {
      onClick?.(event);
      if (!event.defaultPrevented && follows(event, event.currentTarget)) {
        event.preventDefault();
        visit(href, { replace, preserveScroll });
      }
    },
  });
}

function follows(event, anchor) {
  if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return false;
  if (!anchor || anchor.target && anchor.target !== '_self' || anchor.hasAttribute('download') || anchor.dataset.omnibridge === 'false') return false;
  const url = new URL(anchor.href, window.location.href);
  if (url.origin !== window.location.origin || !url.pathname.startsWith(options.prefix)) return false;
  // A link to a place of the page itself: the browser's.
  return !(url.pathname === window.location.pathname && url.search === window.location.search && url.hash);
}

function App({ initial }) {
  const [page, setPage] = useState(initial);
  current = page;
  useEffect(() => {
    navigate = setPage;
    const onPop = (event) => {
      const state = event.state?.omnibridge;
      state ? setPage(state) : visit(window.location.href, { replace: true });
    };
    window.addEventListener('popstate', onPop);
    return () => window.removeEventListener('popstate', onPop);
  }, []);
  const Component = options.resolve(page.component);
  if (!Component) throw new Error(`omnibridge/react: no component "${page.component}"`);
  return createElement(PageContext.Provider, { value: page }, createElement(Component, page.props));
}

/**
 * Render the page the shell holds (data-page on the root element) and follow
 * the links under prefix within the front end; the others leave it.
 */
export function createApp({ resolve, el = document.getElementById('app'), prefix = '/' }) {
  options = { resolve, prefix };
  const initial = JSON.parse(el.dataset.page);
  window.history.replaceState({ omnibridge: initial }, '', initial.url);
  // Plain links inside the pages (rendered HTML: an article's text) are followed too.
  el.addEventListener('click', (event) => {
    const anchor = event.target.closest?.('a[href]');
    if (anchor && !event.defaultPrevented && follows(event, anchor)) {
      event.preventDefault();
      visit(anchor.href);
    }
  });
  createRoot(el).render(createElement(App, { initial }));
}
