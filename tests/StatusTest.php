<?php

declare(strict_types=1);

namespace FFI\Env\Tests;

use FFI\Env\Status;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Status::class)]
final class StatusTest extends TestCase
{
    public function testStatusesAreUnique(): void
    {
        $statuses = (new \ReflectionClass(Status::class))->getConstants();

        self::assertCount(4, $statuses);
        self::assertSame($statuses, \array_unique($statuses));
    }
}
