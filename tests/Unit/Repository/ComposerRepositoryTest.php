<?php

/**
 * @noinspection UnnecessaryAssertionInspection
 */

declare(strict_types=1);

namespace Jascha030\Bitbucket2Github\Repository;

use InvalidArgumentException;
use Jascha030\Bitbucket2Github\OutputFilesTrait;
use PHPUnit\Framework\TestCase;
use Seld\JsonLint\ParsingException;

/**
 * @internal
 *
 * @covers \Jascha030\Bitbucket2Github\Repository\ComposerRepository
 */
final class ComposerRepositoryTest extends TestCase
{
    use OutputFilesTrait;

    private const COMPOSER_JSON_INVALID = <<<'JSON'
{
  "require": {
    "php": ">=5.3"
  }
}
JSON;

    /**
     * @depends testConstruct
     */
    public function testGetName(): void
    {
        self::assertEquals('bb2gh', $this->getRepository()->getName());
    }

    /**
     * @depends testConstruct
     */
    public function testGetDescription(): void
    {
        self::assertEquals('CLI tool to help me quickly migrate a project from bitbucket to github.', $this->getRepository()->getDescription());
    }

    /**
     * @depends testConstruct
     */
    public function testGetVendor(): void
    {
        self::assertEquals('jascha030', $this->getRepository()->getVendor());
    }

    /**
     * @depends testConstruct
     *
     * @throws \Seld\JsonLint\ParsingException
     */
    public function testThrowsOnMissingName(): void
    {
        $this->createFile('composer.json', self::COMPOSER_JSON_INVALID);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Could not get package name from composer.json in path: ' . self::$outputDir);

        new ComposerRepository(self::$outputDir);
    }

    public function testConstruct(): void
    {
        self::assertInstanceOf(RepositoryInterface::class, $this->getRepository());
    }

    private function getRepository(): ComposerRepository
    {
        try {
            return new ComposerRepository(__DIR__ . '/../../../');
        } catch (ParsingException $e) {
            self::fail($e->getMessage());
        }
    }
}
