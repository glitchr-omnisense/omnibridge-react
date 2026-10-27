---
title: Testing
order: 60
---

# Testing

## Testing an application

`Protocol::answer()` is a function of its arguments: a controller's test asks the page three ways -
no header (the shell, `data-page` holding the page), `X-Omnibridge: true` (the JSON), another
`X-Omnibridge-Version` (409) - and compares, as the package's own tests do. The JSON a host gives for
a screen is compared between hosts: lesringostars' parity suite checks that its Symfony site and its
Laravel copy give the same pages to lesringostars-react.

## The package's suite

```sh
docker run --rm -v "$PWD":/app -w /app lesringostars-laravel-app vendor/bin/phpunit tests
```

`ProtocolTest`: the first visit is the shell holding the page (the JSON in `data-page` decodes to the
page); a visit within the site is the page in JSON (its version header, `Vary`, Unicode as it is); a
front end of another version reloads (409 and the location).

The JavaScript has no test in this package: it is exercised by lesringostars-react (its six tests and
its build). The CI (GitHub Actions, glitchr-studio/ci's package workflow) runs PHPUnit and PHPStan.
