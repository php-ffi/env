<?php

/**
 * This file is part of FFI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FFI\Env;

/**
 * @psalm-type StatusType = Status::*
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
