# Behat Unused Step Definitions Extension

Do you have a large Behat test suite? Would you like to check your feature
contexts for unused step definitions?
This Behat extension detects and reports step definitions that are not being
used in any of your `*.feature` files.

[![Build status](https://img.shields.io/github/workflow/status/nicwortel/behat-unused-step-definitions-extension/CI)](https://github.com/nicwortel/behat-unused-step-definitions-extension/actions)
[![License](https://img.shields.io/github/license/nicwortel/behat-unused-step-definitions-extension)](https://github.com/nicwortel/behat-unused-step-definitions-extension/blob/master/LICENSE.txt)
[![Required PHP version](https://img.shields.io/packagist/php-v/nicwortel/behat-unused-step-definitions-extension)](https://github.com/nicwortel/behat-unused-step-definitions-extension/blob/master/composer.json)
[![Current version](https://img.shields.io/packagist/v/nicwortel/behat-unused-step-definitions-extension)](https://packagist.org/packages/nicwortel/behat-unused-step-definitions-extension)

![Screenshot](docs/screenshot.png)

## Installation

```bash
composer require --dev nicwortel/behat-unused-step-definitions-extension
```

Activate the extension in your `behat.yml`:

```yaml
default:
  extensions:
    NicWortel\BehatUnusedStepDefinitionsExtension\Extension: ~
```

## Usage

After following the installation steps as documented above, simply run Behat.
Instead of actually executing the tests, a dry run will be enough to collect
information about unused step definitions:

```bash
vendor/bin/behat --dry-run
```

Note that if you have multiple suites, the unused step definitions will be
listed per suite after the suite has finished.
