<?php

declare(strict_types=1);

namespace Jascha030\Bitbucket2Github\Repository;

interface RepositoryInterface
{
    public function getName(): string;

    public function getDescription(): ?string;
}
