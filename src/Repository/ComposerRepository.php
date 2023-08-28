<?php

declare(strict_types=1);

namespace Jascha030\Bitbucket2Github\Repository;

use Composer\Config;
use Composer\Factory;
use Composer\Json\JsonFile;
use Composer\Package\RootPackageInterface;
use InvalidArgumentException;
use RuntimeException;
use function explode;
use function is_array;

class ComposerRepository implements RepositoryInterface
{
    private JsonFile $jsonFile;

    private string $packageName;

    private ?string $description;

    /**
     * @throws \Seld\JsonLint\ParsingException
     */
    public function __construct(private readonly string $path)
    {
        if (! $this->getJsonFile()->exists()) {
            throw new \InvalidArgumentException('Composer.json does not exist in ' . $this->path);
        }

        $contents = $this->getJsonFile()->read();

        if (! is_array($contents)) {
            throw new \InvalidArgumentException('Could not parse composer.json in path: ' . $this->path);
        }

        $name = $contents['name'] ?? null;

        if (empty($name)) {
            throw new InvalidArgumentException('Could not get package name from composer.json in path: ' . $this->path);
        }

        $this->packageName = $name;
        $this->description = $contents['description'] ?? null;
    }

    private function getJsonFile(): JsonFile
    {
        if (! isset($this->jsonFile)) {
            $this->jsonFile = new JsonFile($this->path . '/composer.json');
        }

        return $this->jsonFile;
    }

    public function getName(): string
    {
        return explode('/', $this->packageName)[1] ?? throw new RuntimeException('Could not get package name from composer.json');
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
