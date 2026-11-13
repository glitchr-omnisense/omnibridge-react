# Changelog

## [Unreleased]

## [1.0.0] - 2026-11-13

The first stable release: `composer require omnibridge/react` installs with Composer's default
stability, and `github:glitchr-omnisense/omnibridge-react#v1.0.0` gives the JavaScript package at the
same version (package.json's `version` is 1.0.0, it was 0.1.0).

### Added

- PHP, framework-free: `Page` (component, props, url, version) and `Protocol::answer()`. The first
  visit is an HTML shell holding the page in `data-page`; a visit within the site (`X-Omnibridge`)
  gets the page in JSON; a front end of another version reloads (409, `X-Omnibridge-Location`).
- JavaScript (`@omnibridge/react`, plain ES modules under `js/src`, React and React DOM 18 or later as
  peer dependencies): `createApp()`, rendering the first page at once (`flushSync`); `Link` and the
  page's own links under the prefix followed without reloading (`visit()`); back and forward;
  `fetchPage()`; `usePage()`; the types in `index.d.ts`.
- package.json at the repository's root, so that an application depends on a tag or a commit of this
  repository.
- CI: glitchr-studio/ci's package workflow (PHPUnit, PHPStan at max against the baseline, a consumer
  install); a release workflow on a pushed tag.
- The manual in `docs/`.
