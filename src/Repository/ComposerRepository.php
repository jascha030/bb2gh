<?php

declare(strict_types=1);

namespace Jascha030\Bitbucket2Github\Repository;

use Composer\Json\JsonFile;
use InvalidArgumentException;
use RuntimeException;

use function explode;

class ComposerRepository implements RepositoryInterface
{
    private JsonFile $jsonFile;

    private string $fullPackageName;

    private ?string $description;

    /**
     * @throws \Seld\JsonLint\ParsingException
     */
    public function __construct(private readonly string $path)
    {
        $contents = $this->getJsonFile()->read();
        $name     = $contents['name'] ?? null;

        if (empty($name)) {
            throw new InvalidArgumentException('Could not get package name from composer.json in path: ' . $this->path);
        }

        $this->fullPackageName = $name;
        $this->description     = $contents['description'] ?? null;
    }

    private function getJsonFile(): JsonFile
    {
        if (! isset($this->jsonFile)) {
            $this->jsonFile = new JsonFile($this->path . '/composer.json');
        }

        return $this->jsonFile;
    }

    public function getVendor(): string
    {
        return explode('/', $this->fullPackageName)[0] ?? throw new RuntimeException('Could not get package name from composer.json');
    }

    public function getName(): string
    {
        return explode('/', $this->fullPackageName)[1] ?? throw new RuntimeException('Could not get package name from composer.json');
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
