<?php

declare(strict_types=1);

use FFI\Env\Exception\EnvironmentException;
use FFI\Env\Runtime;

require __DIR__ . '/../../vendor/autoload.php';

$usable = false;

if (\class_exists(FFI::class, false)) {
    try {
        FFI::cdef();
        $usable = true;
    } catch (Throwable $_) {
        $usable = false;
    }
}

try {
    Runtime::assertAvailable();
    $asserted = true;
} catch (EnvironmentException $_) {
    $asserted = false;
}

echo \serialize([
    'sapi' => \PHP_SAPI,
    'loaded' => \extension_loaded('ffi'),
    'status' => Runtime::getStatus(),
    'available' => Runtime::isAvailable(),
    'asserted' => $asserted,
    'usable' => $usable,
]), "\n";
