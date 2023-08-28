<?php

/**
 * @noinspection UnnecessaryAssertionInspection
 */

declare(strict_types=1);

namespace Jascha030\Bitbucket2Github\Executable;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function is_executable;

use const PHP_BINARY;

/**
 * @internal
 *
 * @covers \Jascha030\Bitbucket2Github\Executable\Executable
 */
final class ExecutableTest extends TestCase
{
    /**
     * @depends testGetExecutable
     */
    public function testToString(): void
    {
        self::assertEquals(PHP_BINARY, (string) $this->getPhp());
    }

    /**
     * @depends testConstruct
     */
    public function testGetExecutable(): void
    {
        self::assertEquals(PHP_BINARY, $this->getPhp()->getExecutable());
    }

    /**
     * @depends testConstruct
     */
    public function testFindsExecutableByName(): void
    {
        $class = new Executable('php');

        self::assertTrue(@is_executable($class->getExecutable()));
    }

    /**
     * @depends testConstruct
     */
    public function testThrowsOnNonExistingPath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Binary "/path/doesnt/exist" not found.');

        new Executable('/path/doesnt/exist');
    }

    /**
     * @depends testConstruct
     */
    public function testThrowsOnNonExecutablePath(): void
    {
        $file = __DIR__ . '/../../Fixtures/bin/not_executable';
        @chmod($file, 0644);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Binary not found or not executable');

        new Executable(__DIR__ . '/../../Fixtures/bin/not_executable');
    }

    public function testConstruct(): void
    {
        self::assertInstanceOf(Executable::class, $this->getPhp());
    }

    private function getPhp(): Executable
    {
        return new Executable(PHP_BINARY);
    }
}
