---
title: Verified, and not
order: 90
---

# Verified, and not

## What the tests prove

Run on 2026-10-27 in `lesringostars-laravel-app` (PHP 8.4.27), the checkout's own `vendor/`, no
network: 3 tests, 11 assertions, green. The [Installation](installation.md) page's script was run in
the same image that day and its output pasted.

## Verified elsewhere

- lesringostars' blog and articles through lesringostars-react, given by the Symfony site and by its
  Laravel copy from the same screens (its six tests, its build, the parity suite).
- `createApp()` rendering at once (`flushSync`, commit 2547b2a): found on lesringostars, whose layout's
  scripts loaded after the front end did not find their elements.

## What is not verified

- The JavaScript in this repository: no test of its own, and not run for this manual (no Node.js
  harness here); what [The JavaScript](javascript.md) says is read from `js/src/index.js`.
- React 18: the peer dependency admits it; lesringostars-react's version is the one exercised.
- The Symfony and Laravel snippets of [The protocol](protocol.md#the-answer-in-a-response): written
  against the classes' signatures, not run.
