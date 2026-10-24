<?php

declare(strict_types=1);

namespace FFI\Env;

/**
 * Values of the `ffi.enable` directive as seen by {@see Runtime}.
 *
 * @psalm-type StatusType = Status::*
 *
 * @phpstan-type StatusType Status::*
 */
final class Status
{
    /**
     * The `ffi` extension is not loaded.
     *
     * @var StatusType
     */
    public const NOT_AVAILABLE = 0x00;

    /**
     * The extension is loaded but disabled by `ffi.enable=0`, `ffi.enable=false`
     * or by any other value that is neither a true boolean nor `preload`.
     *
     * @var StatusType
     */
    public const DISABLED = 0x01;

    /**
     * The extension is loaded and enabled in every SAPI (`ffi.enable=1`).
     *
     * @var StatusType
     */
    public const ENABLED = 0x02;

    /**
     * The extension is loaded with `ffi.enable=preload`: the FFI API is
     * available in the `cli` SAPI and in preload scripts only.
     *
     * @var StatusType
     */
    public const CLI_ENABLED = 0x03;
}
