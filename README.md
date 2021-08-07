# FFI Environment

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

assert(Runtime::assertAvailable(), EnvironmentException::getErrorMessageFromStatus());
```
