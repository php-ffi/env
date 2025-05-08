<?php

declare(strict_types=1);

namespace FFI\Env;

use FFI\Env\Exception\EnvironmentException;

/**
 * @psalm-import-type StatusType from Status
 *
 * @phpstan-import-type StatusType from Status
 */
final class Runtime
{
    /**
     * @var non-empty-string
     */
    private const EXT_NAME = 'FFI';

    /**
     * @var non-empty-string
     */
    private const EXT_CONFIG_NAME = 'ffi.enable';

    /**
     * @param StatusType|null $status
     */
    public static function assertAvailable(?int $status = null): bool
    {
        $status ??= self::getStatus();

        if (self::isAvailable($status)) {
            return true;
        }

        throw EnvironmentException::fromStatus($status);
    }

    /**
     * Returns {@see true} if the current environment (SAPI) supports foreign
     * function interface headers execution/compilation or {@see false} instead.
     *
     * @param StatusType|null $status
     */
    public static function isAvailable(?int $status = null): bool
    {
        $status ??= self::getStatus();

        if ($status === Status::CLI_ENABLED) {
            return \in_array(\strtolower(\PHP_SAPI), ['cli', 'phpdbg', 'embed'], true);
        }

        return $status === Status::ENABLED;
    }

    /**
     * Returns FFI status.
     *
     * @return StatusType
     */
    public static function getStatus(): int
    {
        if (!\extension_loaded(self::EXT_NAME)) {
            return Status::NOT_AVAILABLE;
        }

        switch (self::fetchConfig()) {
            case '1':
                return Status::ENABLED;

            case '0':
                return Status::DISABLED;

            default:
                return Status::CLI_ENABLED;
        }
    }

    protected static function fetchConfig(): string
    {
        // - Returns "1" in case of 'ffi.enable=true' or 'ffi.enable=1' in php.ini
        // - Returns "" (empty string) in case of 'ffi.enable=false' or 'ffi.enable=0' in php.ini
        // - Returns "0" in case of direct execution `php -dffi.enable=0 file.php`
        $config = \ini_get(self::EXT_CONFIG_NAME);

        if ((bool) $config === false) {
            return '0';
        }

        return \strtolower($config);
    }
}
