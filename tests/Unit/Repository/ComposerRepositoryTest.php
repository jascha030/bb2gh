<?php

/**
 * @noinspection UnnecessaryAssertionInspection
 */

declare(strict_types=1);

namespace Jascha030\Bitbucket2Github\Repository;

use PHPUnit\Framework\TestCase;
use Seld\JsonLint\ParsingException;

/**
 * @internal
 *
 * @covers \Jascha030\Bitbucket2Github\Repository\ComposerRepository
 */
final class ComposerRepositoryTest extends TestCase
{
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
