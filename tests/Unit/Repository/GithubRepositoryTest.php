<?php

declare(strict_types=1);

namespace Jascha030\Bitbucket2Github\Repository;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \Jascha030\Bitbucket2Github\Repository\GithubRepository
 */
final class GithubRepositoryTest extends TestCase
{
    /**
     * @depends testSetDescription
     */
    public function testGetName(): void
    {
        self::assertEquals('bb2gh', $this->getRepository()->getName());
    }

    /**
     * @depends testSetVendor
     */
    public function testGetVendor(): void
    {
        self::assertEquals('Jascha030', $this->getRepository()->getVendor());
    }

    /**
     * @depends testConstruct
     */
    public function testSetVendor(): void
    {
        $repository = $this->getRepository();
        $repository->setVendor('Jascha');

        self::assertEquals('Jascha', $repository->getVendor());
    }

    /**
     * @depends testConstruct
     */
    public function testSetDescription(): void
    {
        $repository = $this->getRepository();
        $repository->setDescription('test');

        self::assertEquals('test', $repository->getDescription());
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

    private function getRepository(): GithubRepository
    {
        return new GithubRepository('bb2gh', 'Jascha030', 'CLI tool to help me quickly migrate a project from bitbucket to github.');
    }
}
