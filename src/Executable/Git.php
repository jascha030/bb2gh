<?php

declare(strict_types=1);

namespace Jascha030\Bitbucket2Github\Executable;

final class Git extends Executable
{
    public function __construct(string $executable = 'git')
    {
        parent::__construct($executable);
    }
}
