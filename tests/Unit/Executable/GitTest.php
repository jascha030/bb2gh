<?php

declare(strict_types=1);

namespace Jascha030\Bitbucket2Github\Executable;

use PHPUnit\Framework\TestCase;
use function is_executable;

/**
 * @internal
 *
 * @covers \Jascha030\Bitbucket2Github\Executable\Executable
 * @covers \Jascha030\Bitbucket2Github\Executable\Git
 */
final class GitTest extends TestCase
{
    public function testConstruct(): void
    {
        self::assertTrue(@is_executable((string) new Git()));
    }
}
