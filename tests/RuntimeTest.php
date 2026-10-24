<?php

declare(strict_types=1);

namespace FFI\Env\Tests;

use FFI\Env\Exception\EnvironmentException;
use FFI\Env\Runtime;
use FFI\Env\Status;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(Runtime::class)]
final class RuntimeTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{string, int}>
     */
    public static function configDataProvider(): iterable
    {
        yield 'one' => ['1', Status::ENABLED];
        yield 'true' => ['true', Status::ENABLED];
        yield 'yes' => ['yes', Status::ENABLED];
        yield 'on' => ['On', Status::ENABLED];
        yield 'any positive number' => ['2', Status::ENABLED];
        yield 'negative number' => ['-1', Status::ENABLED];
        yield 'zero' => ['0', Status::DISABLED];
        yield 'false' => ['false', Status::DISABLED];
        yield 'off' => ['off', Status::DISABLED];
        yield 'none' => ['none', Status::DISABLED];
        yield 'empty string' => ['', Status::DISABLED];
        yield 'unknown word' => ['foo', Status::DISABLED];
        yield 'preload' => ['preload', Status::CLI_ENABLED];
        yield 'preload in upper case' => ['PRELOAD', Status::CLI_ENABLED];
        yield 'preload in mixed case' => ['Preload', Status::CLI_ENABLED];
        yield 'preload with leading space' => [' preload', Status::DISABLED];
    }

    /**
     * @dataProvider configDataProvider
     */
    #[DataProvider('configDataProvider')]
    public function testStatusFollowsTheEngineInterpretationOfConfig(string $value, int $expected): void
    {
        $this->skipIfExtensionIsMissing();

        $report = $this->runProbe(\PHP_BINARY, ['ffi.enable=' . $value]);

        self::assertSame($expected, $report['status']);
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function configValueDataProvider(): iterable
    {
        foreach (self::configDataProvider() as $name => [$value]) {
            yield $name => [$value];
        }
    }

    /**
     * @dataProvider configValueDataProvider
     */
    #[DataProvider('configValueDataProvider')]
    public function testAvailabilityMatchesRealApiAccessInCli(string $value): void
    {
        $this->skipIfExtensionIsMissing();

        $report = $this->runProbe(\PHP_BINARY, ['ffi.enable=' . $value]);

        self::assertSame('cli', $report['sapi']);
        self::assertSame($report['usable'], $report['available']);
        self::assertSame($report['usable'], $report['asserted']);
    }

    public function testPreloadIsNotAvailableInCgi(): void
    {
        $this->skipIfExtensionIsMissing();

        $report = $this->runProbe($this->getSiblingBinary('php-cgi'), ['ffi.enable=preload'], ['-q']);

        self::assertSame(Status::CLI_ENABLED, $report['status']);
        self::assertFalse($report['available']);
        self::assertFalse($report['usable']);
    }

    public function testPreloadIsNotAvailableInPhpdbg(): void
    {
        $this->skipIfExtensionIsMissing();

        $report = $this->runProbe($this->getSiblingBinary('phpdbg'), ['ffi.enable=preload'], ['-qrr']);

        self::assertSame('phpdbg', $report['sapi']);
        self::assertSame(Status::CLI_ENABLED, $report['status']);
        self::assertFalse($report['available']);
        self::assertFalse($report['usable']);
    }

    public function testEnabledIsAvailableInCgi(): void
    {
        $this->skipIfExtensionIsMissing();

        $report = $this->runProbe($this->getSiblingBinary('php-cgi'), ['ffi.enable=1'], ['-q']);

        self::assertSame(Status::ENABLED, $report['status']);
        self::assertTrue($report['available']);
        self::assertTrue($report['usable']);
    }

    public function testMissingExtensionIsNotAvailable(): void
    {
        $report = $this->runProbe(\PHP_BINARY, [], ['-n']);

        if ($report['loaded']) {
            self::markTestSkipped('The "ffi" extension is built into this PHP');
        }

        self::assertSame(Status::NOT_AVAILABLE, $report['status']);
        self::assertFalse($report['available']);
        self::assertFalse($report['asserted']);
    }

    public function testEnabledStatusIsAvailable(): void
    {
        self::assertTrue(Runtime::isAvailable(Status::ENABLED));
        self::assertTrue(Runtime::assertAvailable(Status::ENABLED));
    }

    public function testNotAvailableAndDisabledStatusesAreUnavailable(): void
    {
        self::assertFalse(Runtime::isAvailable(Status::NOT_AVAILABLE));
        self::assertFalse(Runtime::isAvailable(Status::DISABLED));
    }

    public function testPreloadStatusIsAvailableOnlyInCliSapi(): void
    {
        self::assertSame(\PHP_SAPI === 'cli', Runtime::isAvailable(Status::CLI_ENABLED));
    }

    public function testAssertionFailureCarriesTheStatus(): void
    {
        $this->expectException(EnvironmentException::class);
        $this->expectExceptionCode(Status::DISABLED);

        Runtime::assertAvailable(Status::DISABLED);
    }
}
