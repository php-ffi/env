<?php

declare(strict_types=1);

namespace FFI\Env;

use FFI\Env\Exception\EnvironmentException;

/**
 * Describes whether the FFI API can be used in the current environment.
 *
 * @psalm-import-type StatusType from Status
 *
 * @phpstan-import-type StatusType from Status
 */
final class Runtime
{
    /**
     * @var non-empty-string
     */
    private const EXT_NAME = 'ffi';

    /**
     * @var non-empty-string
     */
    private const EXT_CONFIG_NAME = 'ffi.enable';

    /**
     * The `ffi.enable` value that restricts the FFI API to the CLI SAPI and preload scripts.
     *
     * @var non-empty-string
     */
    private const CONFIG_PRELOAD = 'preload';

    /**
     * The only SAPI in which `ffi.enable=preload` allows the FFI API outside of preload scripts.
     *
     * @var non-empty-string
     */
    private const SAPI_CLI = 'cli';

    /**
     * Throws if the FFI API is not available in the current environment.
     *
     * @param StatusType|null $status
     * @throws EnvironmentException in case the FFI API is not available
     */
    public static function assertAvailable(?int $status = null): bool
    {
        $status = $status ?? self::getStatus();

        if (self::isAvailable($status)) {
            return true;
        }

        throw EnvironmentException::fromStatus($status);
    }

    /**
     * Returns {@see true} if the current SAPI allows executing FFI code
     * outside of preload scripts.
     *
     * @param StatusType|null $status
     */
    public static function isAvailable(?int $status = null): bool
    {
        $status = $status ?? self::getStatus();

        if ($status === Status::CLI_ENABLED) {
            return \PHP_SAPI === self::SAPI_CLI;
        }

        return $status === Status::ENABLED;
    }

    /**
     * Returns the state of the `ffi` extension and its `ffi.enable` directive.
     *
     * @return StatusType
     */
    public static function getStatus(): int
    {
        if (!\extension_loaded(self::EXT_NAME)) {
            return Status::NOT_AVAILABLE;
        }

        $config = \ini_get(self::EXT_CONFIG_NAME);

        if ($config === false) {
            return Status::NOT_AVAILABLE;
        }

        if (\strcasecmp($config, self::CONFIG_PRELOAD) === 0) {
            return Status::CLI_ENABLED;
        }

        return self::isTrueBoolean($config) ? Status::ENABLED : Status::DISABLED;
    }

    /**
     * Tells whether the `ffi.enable` value is a true boolean.
     */
    private static function isTrueBoolean(string $value): bool
    {
        if (\in_array(\strtolower($value), ['true', 'yes', 'on'], true)) {
            return true;
        }

        return \preg_match('/^\s*([+-]?\d+)/', $value, $matches) === 1
            && (int) $matches[1] !== 0;
    }
}
