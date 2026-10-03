# TD-PHP

A disciplined PHP project foundation with a preconfigured development toolchain for testing, static analysis, and code quality.

> **Current scope:** the repository is currently structured around the Nex WordPress framework. The quality configuration is intentionally reusable, but the application namespace and WordPress integration remain Nex-specific.

## Goals

TD-PHP is intended to make a new PHP project start from a known engineering baseline instead of accumulating tooling and conventions later.

The repository currently provides:

- PHP 8.2+ as the supported runtime baseline.
- PHPUnit 11 for automated PHP tests.
- PHPStan for static analysis.
- Psalm for an independent static-analysis pass.
- PHP CS Fixer for deterministic code formatting.
- Composer scripts that separate formatting from verification.
- PHPUnit source configuration for future coverage reporting.
- A small, conventional `src/` and `tests/` layout.

No additional testing framework or quality tool is required by this baseline.

## Requirements

- PHP 8.2 or newer.
- Composer 2.x.
- Node.js/npm are required only for the existing frontend build tooling.

Install PHP dependencies:

~~~bash
composer install
~~~

## PHP quality workflow

### Run tests

~~~bash
composer test
~~~

### Generate an HTML coverage report

~~~bash
composer test:coverage
~~~

The report is written to `build/coverage/`.

### Run PHPStan

~~~bash
composer analyze:phpstan
~~~

PHPStan is configured at level 9. The intent is to keep the baseline strict rather than gradually accumulating a large type-analysis backlog.

### Run Psalm

~~~bash
composer analyze:psalm
~~~

Psalm remains enabled alongside PHPStan. They are deliberately kept as separate analysis passes because they can expose different classes of type and design problems.

### Check formatting without modifying files

~~~bash
composer format:check
~~~

### Format PHP files

~~~bash
composer format
~~~

Formatting is a write operation. It should not be used as the verification step in CI or before reviewing a change.

### Run the complete PHP quality gate

~~~bash
composer check-all
~~~

`check-all` is verification-only: it checks formatting, runs both static analysers, and executes the PHPUnit suite. It does not rewrite source files.

## Configuration

### PHPUnit

`phpunit.xml`:

- bootstraps Composer autoloading;
- discovers tests under `tests/`;
- scopes the coverage source to `src/`;
- writes HTML coverage to `build/coverage/`;
- treats risky tests and warnings as failures.

Coverage reporting is available, but this skeleton does not impose an arbitrary coverage percentage. Coverage is a diagnostic signal, not a substitute for meaningful tests.

### PHPStan

`phpstan.neon` analyses `src/` at level 9.

The project intentionally starts strict. For a new codebase, weakening analysis first and repairing a large backlog later is usually the more expensive direction.

### Psalm

`psalm.xml` uses Psalm's strict error level and analyses `src/`.

### PHP CS Fixer

Both existing configuration filenames are retained and contain the same rules:

- PSR-12 baseline;
- short array syntax;
- deterministic import ordering;
- unused-import removal;
- multiline array trailing commas;
- explicit operator spacing;
- no risky rules.

The finder is restricted to `src/` and `tests/`, so generated files, dependencies, and unrelated project files are not formatted accidentally.

## Project structure

~~~text
.
├── src/                 # PHP source
├── tests/               # PHPUnit tests
├── assets/              # Existing frontend assets
├── composer.json        # PHP dependencies and quality commands
├── phpunit.xml          # PHPUnit configuration
├── phpstan.neon         # PHPStan configuration
├── psalm.xml            # Psalm configuration
├── .php-cs-fixer.dist.php
├── php-cs-fixer.dist.php
└── README.md
~~~

The duplicate CS Fixer configuration files are currently kept for compatibility with the repository state. They are intentionally identical so that configuration discovery cannot produce two different formatting policies.

## Engineering principles

This repository follows a deliberately small quality baseline:

1. Prefer language and framework features over custom infrastructure.
2. Keep tests deterministic and fast.
3. Treat static analysis as a design feedback mechanism, not just a CI checkbox.
4. Keep formatting deterministic and non-risky.
5. Do not introduce another tool unless it solves a demonstrated problem.
6. Do not make coverage percentages the primary measure of test quality.
7. Keep the default workflow understandable enough that a contributor can run it locally without learning a bespoke command system.

## Before opening a pull request

Run:

~~~bash
composer install
composer check-all
~~~

Then inspect the diff and make sure generated output under `build/` has not been added to the commit.

## Current boundary

The current repository still contains Nex/WordPress-specific application code and an existing JavaScript build/test toolchain. Those are not removed or replaced by this quality pass.

The next architectural step should be decided separately: either make TD-PHP a genuinely generic PHP skeleton, or explicitly define it as the reusable engineering foundation for Nex/WordPress projects. Mixing those identities long-term will make the template harder to understand and maintain.
