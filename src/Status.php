<?php

declare(strict_types=1);

namespace FFI\Env;

/**
 * @psalm-type StatusType = Status::*
 *
 * @phpstan-type StatusType Status::*
 */
final class Status
{
    /**
     * @var StatusType
     */
    public const NOT_AVAILABLE = 0x00;

    /**
     * @var StatusType
     */
    public const DISABLED = 0x01;

    /**
     * @var StatusType
     */
    public const ENABLED = 0x02;

    /**
     * @var StatusType
     */
    public const CLI_ENABLED = 0x03;
}
