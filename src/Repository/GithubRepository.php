<?php

declare(strict_types=1);

namespace Jascha030\Bitbucket2Github\Repository;

class GithubRepository implements RepositoryInterface
{
    public function __construct(
        readonly private string $name,
        private ?string $vendor = null,
        private ?string $description = null
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVendor(): ?string
    {
        return $this->vendor ?? null;
    }

    public function setVendor(string $vendor): self
    {
        $this->vendor = $vendor;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description ?? null;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
