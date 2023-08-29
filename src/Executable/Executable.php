<?php

declare(strict_types=1);

namespace Jascha030\Bitbucket2Github\Executable;

use InvalidArgumentException;
use RuntimeException;
use Stringable;
use Symfony\Component\Process\ExecutableFinder;

use function is_executable;
use function is_file;
use function realpath;

class Executable implements Stringable
{
    private string $executable;

    public function __construct(string $executable)
    {
        if (! @is_file($executable)) {
            $executable = (new ExecutableFinder())->find($executable) ?? throw new InvalidArgumentException('Binary "' . $executable . '" not found.');
        }

        if (! @is_file($executable) || ! @is_executable($executable)) {
            throw new RuntimeException('Binary not found or not executable.');
        }

        $realPath = @realpath($executable);

        $this->executable = false !== $realPath ? $realPath : $executable;
    }

    public function getExecutable(): string
    {
        return $this->executable;
    }

    public function __toString(): string
    {
        return $this->getExecutable();
    }
}
