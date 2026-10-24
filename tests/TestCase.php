<?php

declare(strict_types=1);

namespace FFI\Env\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    private const PROBE = __DIR__ . '/Fixtures/probe.php';

    /**
     * Returns the FFI state reported by a PHP binary started with the given settings.
     *
     * @param list<string> $directives ini directives passed as `-d` options
     * @param list<string> $arguments binary options placed before the probe script
     * @return array{sapi: string, loaded: bool, status: int, available: bool, asserted: bool, usable: bool}
     */
    protected function runProbe(string $binary, array $directives = [], array $arguments = []): array
    {
        $command = [$binary];

        $directives = \array_merge(['opcache.enable=0', 'opcache.enable_cli=0'], $directives);

        foreach ($directives as $directive) {
            $command[] = '-d';
            $command[] = $directive;
        }

        $command = \array_merge($command, $arguments, [self::PROBE]);

        $process = \proc_open($command, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);

        if (!\is_resource($process)) {
            self::fail('Unable to start "' . $binary . '"');
        }

        \fclose($pipes[0]);
        $output = (string) \stream_get_contents($pipes[1]);
        $errors = (string) \stream_get_contents($pipes[2]);
        \fclose($pipes[1]);
        \fclose($pipes[2]);
        \proc_close($process);

        $lines = \array_values(\array_filter(\array_map('trim', \explode("\n", $output))));
        $report = \unserialize((string) \end($lines), ['allowed_classes' => false]);

        if (!\is_array($report)) {
            self::fail('Unexpected probe output: ' . $output . $errors);
        }

        return $report;
    }

    /**
     * Returns the path to a sibling SAPI binary of the running PHP or skips the test.
     */
    protected function getSiblingBinary(string $name): string
    {
        $directory = \dirname(\PHP_BINARY);

        foreach ([$name, $name . '.exe'] as $file) {
            if (\is_file($directory . \DIRECTORY_SEPARATOR . $file)) {
                return $directory . \DIRECTORY_SEPARATOR . $file;
            }
        }

        self::markTestSkipped('The "' . $name . '" binary is not installed next to PHP');
    }

    protected function skipIfExtensionIsMissing(): void
    {
        if (!\extension_loaded('ffi')) {
            self::markTestSkipped('The "ffi" extension is required');
        }
    }
}
