<?php

declare(strict_types=1);

namespace FFI\Env\Tests\Exception;

use FFI\Env\Exception\EnvironmentException;
use FFI\Env\Status;
use FFI\Env\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(EnvironmentException::class)]
final class EnvironmentExceptionTest extends TestCase
{
    public function testNotAvailableMessage(): void
    {
        self::assertSame(
            'An [ext-ffi] not available',
            EnvironmentException::getErrorMessageFromStatus(Status::NOT_AVAILABLE),
        );
    }

    public function testDisabledMessage(): void
    {
        self::assertSame(
            'An [ext-ffi] disabled in your php.ini',
            EnvironmentException::getErrorMessageFromStatus(Status::DISABLED),
        );
    }

    public function testCliRequiredMessageContainsCurrentSapi(): void
    {
        $message = EnvironmentException::getErrorMessageFromStatus(Status::CLI_ENABLED);

        self::assertStringContainsString('"cli" mode', $message);
        self::assertStringContainsString('"' . \PHP_SAPI . '"', $message);
    }

    public function testUnknownStatusHasGenericMessage(): void
    {
        self::assertSame('Unknown Error', EnvironmentException::getErrorMessageFromStatus(0xFF));
    }

    public function testExceptionCarriesStatusAsCode(): void
    {
        $exception = EnvironmentException::fromStatus(Status::DISABLED);

        self::assertSame(Status::DISABLED, $exception->getCode());
        self::assertSame(EnvironmentException::getErrorMessageFromStatus(Status::DISABLED), $exception->getMessage());
    }

    public function testExceptionKeepsPreviousThrowable(): void
    {
        $previous = new \RuntimeException();

        self::assertSame($previous, EnvironmentException::fromStatus(Status::DISABLED, $previous)->getPrevious());
    }
}
