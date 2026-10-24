<?php

declare(strict_types=1);

namespace FFI\Env\Exception;

use FFI\Env\Runtime;
use FFI\Env\Status;

/**
 * Thrown when the FFI API cannot be used in the current environment.
 *
 * @psalm-import-type StatusType from Status
 *
 * @phpstan-import-type StatusType from Status
 */
class EnvironmentException extends \DomainException
{
    /**
     * @var non-empty-string
     */
    private const ERROR_NOT_AVAILABLE = 'An [ext-ffi] not available';

    /**
     * @var non-empty-string
     */
    private const ERROR_CLI_REQUIRED =
        'An [ext-ffi] can only be run in "cli" mode, but the current mode ' .
        'is "%s" does not meet required runtime parameters';

    /**
     * @var non-empty-string
     */
    private const ERROR_FFI_DISABLED = 'An [ext-ffi] disabled in your php.ini';

    /**
     * @var non-empty-string
     */
    private const ERROR_UNKNOWN = 'Unknown Error';

    /**
     * @param StatusType|null $status
     * @return non-empty-string
     */
    public static function getErrorMessageFromStatus(?int $status = null): string
    {
        switch ($status ?? Runtime::getStatus()) {
            case Status::NOT_AVAILABLE:
                return self::ERROR_NOT_AVAILABLE;

            case Status::CLI_ENABLED:
                return \sprintf(self::ERROR_CLI_REQUIRED, \PHP_SAPI);

            case Status::DISABLED:
                return self::ERROR_FFI_DISABLED;

            default:
                return self::ERROR_UNKNOWN;
        }
    }

    /**
     * @param StatusType|null $status
     */
    public static function fromStatus(?int $status = null, ?\Throwable $previous = null): self
    {
        $status = $status ?? Runtime::getStatus();

        return new self(self::getErrorMessageFromStatus($status), $status, $previous);
    }
}
