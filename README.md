# Smart Gamma PHP Cli Quality Tool

> The interactive tool will allow project's team to keep common development code style standards based on pre-commit git hook   

The tool allows automaticly add git pre-commit hook to local dev machine and runs set of quality assertions:

- PHPLint
- PHPCS
- php-cs-fixer
- PHPMD
- PhpSpec
- PhpUnit (TODO)
- Commit message has Jira feature number i.e AB-123 (TODO)

As additional feature it allows use php-cs-fixer with auto fix of commited files

## Install 

``
composer require --dev 421p/php-quality-tool-cli
``

## Usage

1. php vendor/bin/quality

## PHPCS & php-cs-fixer

Will initiate assertions and if violations in code style will be found in the files applied in commit it will prompt to autofix these violations and rescan files.
Autofixed files will be automaticly added to the latest commit if no `--no-commit-autofixed` option is submitted.

## PHPMD

The tool will scan PhpMD warning, but won't block the commit, but will output the list and prompt to rescan your commited file if you want to fix  via IDE some indicated warnings. 
