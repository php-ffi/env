# FFI Environment

<p align="center">
    <a href="https://packagist.org/packages/ffi/env"><img src="https://poser.pugx.org/ffi/env/require/php?style=for-the-badge" alt="PHP 8.1+"></a>
    <a href="https://packagist.org/packages/ffi/env"><img src="https://poser.pugx.org/ffi/env/version?style=for-the-badge" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/ffi/env"><img src="https://poser.pugx.org/ffi/env/v/unstable?style=for-the-badge" alt="Latest Unstable Version"></a>
    <a href="https://packagist.org/packages/ffi/env"><img src="https://poser.pugx.org/ffi/env/downloads?style=for-the-badge" alt="Total Downloads"></a>
    <a href="https://raw.githubusercontent.com/php-ffi/env/master/LICENSE.md"><img src="https://poser.pugx.org/ffi/env/license?style=for-the-badge" alt="License MIT"></a>
</p>
<p align="center">
    <a href="https://github.com/php-ffi/env/actions"><img src="https://github.com/php-ffi/env/workflows/build/badge.svg"></a>
</p>

A set of API methods for working with the FFI environment.

## Requirements

- PHP >= 7.4

## Installation

Library is available as composer repository and can be installed using the 
following command in a root of your project.

```sh
$ composer require ffi/env
```

## Usage

### Retrieve FFI Status

```php
use FFI\Env\Runtime;

$status = Runtime::getStatus();
```

Status can be be one of:
- `\FFI\Env\Status::NOT_AVAILABLE` - Extension not available.
- `\FFI\Env\Status::DISABLED` - Extension disabled.
- `\FFI\Env\Status::ENABLED` - Extension enabled and available in any environment.
- `\FFI\Env\Status::CLI_ENABLED` - Extension available only in CLI SAPI or using a preload.

### Checking Availability

```php
use FFI\Env\Runtime;

$isAvailable = Runtime::isAvailable();
```

In the case that the environment needs to be checked unambiguously, then you 
can use `assertAvailable()` method:

```php
use FFI\Env\Runtime;

Runtime::assertAvailable();
// Throws an \FFI\Env\Exception\EnvironmentException in case FFI is not available.
```

### Optimization

To check the environment, it is recommended to use the `assert` functionality.

```php
use FFI\Env\Runtime;
use FFI\Env\Exception\EnvironmentException;

assert(Runtime::assertAvailable());

// Or using your own assertion error message:
assert(Runtime::isAvailable(), EnvironmentException::getErrorMessageFromStatus());
```
