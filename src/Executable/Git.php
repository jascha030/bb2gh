<?php

declare(strict_types=1);

namespace Jascha030\Bitbucket2Github\Git;

use Jascha030\Bitbucket2Github\Executable\Executable;

class Git extends Executable
{
    public function getBasename(): string
    {
        return 'git';
    }
}
