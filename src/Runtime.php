<?php

/**
 * This file is part of FFI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Env;

use FFI\Env\Exception\EnvironmentException;
use JetBrains\PhpStorm\ExpectedValues;

/**
 * @psalm-import-type StatusType from Status
 */
final class Runtime
{
    /**
     * @var string
     */
    private const EXT_NAME = 'FFI';

    /**
     * @var string
     */
    private const EXT_CONFIG_NAME = 'ffi.enable';

    /**
     * @param StatusType|null $status
     * @return bool
     */
    public static function assertAvailable(
        #[ExpectedValues(valuesFromClass: Status::class)]
        int $status = null
    ): bool {
        $status ??= self::getStatus();

        if ($status->isRuntimeAvailable()) {
            return true;
        }

        throw EnvironmentException::fromStatus($status);
    }

    /**
     * Returns {@see true} if the current environment (SAPI) supports foreign
     * function interface headers execution/compilation or {@see false} instead.
     *
     * @param StatusType|null $status
     * @return bool
     */
    public static function isAvailable(
        #[ExpectedValues(valuesFromClass: Status::class)]
        int $status = null
    ): bool {
        $status ??= self::getStatus();

        if ($status === Status::CLI_ENABLED) {
            return \strtolower(\PHP_SAPI) === 'cli';
        }

        return $status === Status::ENABLED;
    }

    /**
     * Returns FFI status.
     *
     * @return StatusType
     */
    #[ExpectedValues(valuesFromClass: Status::class)]
    public static function getStatus(): int
    {
        if (! \extension_loaded(self::EXT_NAME)) {
            return Status::NOT_AVAILABLE;
        }

        switch(self::fetchConfig()) {
            case 'true':
            case '1':
                return Status::ENABLED;

            case 'false':
            case '0':
                return Status::DISABLED;

            default:
                return Status::CLI_ENABLED;
        }
    }

    /**
     * @return string
     */
    protected static function fetchConfig(): string
    {
        $config = (string)(\ini_get(self::EXT_CONFIG_NAME) ?: '0');

        return \strtolower($config);
    }
}
