# Contributing

## Development setup

Requirements:

- PHP 8.2+
- Composer 2.x
- Node.js/npm when working on the existing frontend toolchain

Install PHP dependencies:

~~~bash
composer install
~~~

## Verification

Before submitting a change, run:

~~~bash
composer check-all
~~~

This command performs:

1. PHP CS Fixer verification without changing files.
2. PHPStan analysis.
3. Psalm analysis.
4. PHPUnit tests.

If you intentionally need to reformat PHP files, run:

~~~bash
composer format
~~~

Then run `composer check-all` again.

## Tests

PHP tests belong under `tests/`.

Keep tests focused on observable behaviour. Prefer small deterministic tests over broad tests that depend on global state or the WordPress runtime unless integration with WordPress is the behaviour being verified.

Coverage can be inspected with:

~~~bash
composer test:coverage
~~~

There is intentionally no mandatory coverage percentage in the baseline.

## Static analysis

Both PHPStan and Psalm are part of the existing development toolchain. New code should satisfy both analyzers rather than introducing suppressions to make the baseline pass.

When an analyzer reports a real design or typing problem, fix the underlying code where practical. Suppressions should be narrow, justified, and local to the case that cannot be expressed more precisely.

## Formatting

PHP CS Fixer is configured as a non-risky formatter.

Use `composer format` to apply formatting and `composer format:check` to verify it.

Do not add unrelated formatting changes to a functional change.

## Pull requests

A good pull request should:

- solve one coherent problem;
- keep the change as small as practical;
- include or update tests when behaviour changes;
- preserve backwards compatibility unless a breaking change is intentional;
- avoid introducing new infrastructure without a concrete need;
- explain important design or compatibility decisions in the pull request description.

The goal is not to maximise the number of tools or checks. The goal is to keep the codebase predictable, testable, and maintainable.
