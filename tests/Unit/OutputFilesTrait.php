<?php

declare(strict_types=1);

namespace Jascha030\Bitbucket2Github;

use Symfony\Component\Filesystem\Filesystem;

trait OutputFilesTrait
{
    private static string $outputDir = __DIR__ . '/../../../../output';

    private function createFile(string $path, string $contents, bool $cleanDir = true): string
    {
        if ($cleanDir) {
            $this->prepareOutputDir();
        }

        $fullPath = static::$outputDir . '/' . $path;

        (new Filesystem())->dumpFile($fullPath, $contents);

        return $fullPath;
    }

    private function prepareOutputDir(): void
    {
        $fs = new Filesystem();

        if ($fs->exists(static::$outputDir)) {
            $fs->remove(static::$outputDir);
        }

        $fs->mkdir(static::$outputDir);
    }
}
