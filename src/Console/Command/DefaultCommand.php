<?php

declare(strict_types=1);

namespace Jascha030\Bitbucket2Github\Console\Command;

use Generator;
use InvalidArgumentException;
use Jascha030\Bitbucket2Github\Executable\Executable;
use Jascha030\Bitbucket2Github\Executable\Git;
use Jascha030\Bitbucket2Github\Repository\ComposerRepository;
use Jascha030\Bitbucket2Github\Repository\GithubRepository;
use Jascha030\Bitbucket2Github\Repository\RepositoryInterface;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

use function array_filter;
use function count;
use function explode;
use function getcwd;
use function is_dir;
use function is_string;
use function sprintf;
use function str_contains;
use function str_starts_with;

use const PHP_EOL;

final class DefaultCommand extends Command
{
    private const CONTENT_CODEOWNERS = <<<'EOF'
* %s
EOF;

    private string $targetDir;

    private RepositoryInterface $localRepository;

    private RepositoryInterface $targetRepository;

    private Git $git;

    private Executable $gh;

    private InputInterface $input;

    /**
     * @throws \Seld\JsonLint\ParsingException
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;

        $this->validateOrigin();

        $this->targetRepository = new GithubRepository(
            $this->getLocalRepository()->getName(),
            $this->getLocalRepository()->getDescription(),
            $this->input->hasOption('organization') ? $this->input->getOption('organization') : null
        );

        if (Command::FAILURE === $this->removeOrigin()->mustRun()->getExitCode()) {
            throw new RuntimeException('Could not remove origin remote');
        }

        if (Command::FAILURE === $this->createRepo()->mustRun()->getExitCode()) {
            throw new RuntimeException('Could not create repository');
        }

        if (! empty($this->input->getOption('code-owner'))) {
            $this->createGithubFiles();
        }

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this
            ->setName('mv')
            ->setDescription('Move a repository from Bitbucket to Github.')
            ->addArgument(
                'directory',
                InputArgument::OPTIONAL,
                'The directory of the repository, defaults to cwd.',
                getcwd()
            )
            ->addOption(
                '--organization',
                '-o',
                InputOption::VALUE_REQUIRED,
                'The organization name',
            )
            ->addOption(
                '--code-owner',
                '-c',
                InputOption::VALUE_REQUIRED,
                'The owner of the repository.',
                null
            );
    }

    private function getGithubCLI(): Executable
    {
        if (! isset($this->gh)) {
            $this->gh = new Executable('gh');
        }

        return $this->gh;
    }

    private function getGit(): Git
    {
        if (! isset($this->git)) {
            $this->git = new Git();
        }

        return $this->git;
    }

    private function getTargetDir(): string
    {
        if (! isset($this->targetDir)) {
            $directory = $this->input->getArgument('directory');

            $this->targetDir = match (true) {
                ! is_string($directory) => throw new InvalidArgumentException('Directory argument must be a string.'),
                ! is_dir($directory)    => throw new InvalidArgumentException('Directory does not exist.'),
                default                 => $directory
            };
        }

        return $this->targetDir;
    }

    public function getTargetRepository(): RepositoryInterface
    {
        return $this->targetRepository;
    }

    /**
     * @throws \Seld\JsonLint\ParsingException
     */
    private function getLocalRepository(): RepositoryInterface
    {
        if (! isset($this->localRepository)) {
            $this->localRepository = new ComposerRepository($this->getTargetDir());
        }

        return $this->localRepository;
    }

    /**
     * @throws \Seld\JsonLint\ParsingException
     */
    private function createRepo(): Process
    {
        return Process::fromShellCommandline(
            sprintf(
                '%s repo create %s %s',
                $this->getGithubCLI(),
                $this->getTargetName(),
                implode(' ', [...$this->getCreateFlags()])
            )
        );
    }

    /**
     * @throws \Seld\JsonLint\ParsingException
     */
    private function getCreateFlags(): Generator
    {
        $args = array_filter([
            '--private'     => true,
            '--push'        => true,
            '--remote'      => 'origin',
            '--source'      => $this->getTargetDir(),
            '--description' => $this->getLocalRepository()->getDescription(),
        ]);

        yield from array_map(
            static fn (string $k, mixed $v): string => true === $v ? $k : "{$k}=\"{$v}\"",
            array_keys($args),
            array_values($args)
        );
    }

    private function validateOrigin(): void
    {
        $process = Process::fromShellCommandline($this->getGit() . ' remote -v', $this->getTargetDir())->mustRun();

        $lines = array_filter(
            array_filter(explode(PHP_EOL, $process->getOutput()), 'strlen'),
            static fn (string $line): bool => str_contains($line, 'git@github.com')
        );

        if (0 < count($lines)) {
            throw new RuntimeException('Origin already contains a github remote.');
        }
    }

    private function removeOrigin(): Process
    {
        return Process::fromShellCommandline($this->getGit() . ' remote rm origin', $this->getTargetDir());
    }

    private function createGithubFiles(): void
    {
        $owner = $this->input->getOption('code-owner');

        if (! str_starts_with($owner, '@')) {
            $owner = '@' . $owner;
        }

        $fs = new Filesystem();

        if ($fs->exists($this->getTargetDir() . '/.github/CODEOWNERS')) {
            return;
        }

        $fs->mkdir($this->getTargetDir() . '/.github');
        $fs->touch($this->getTargetDir() . '/.github/CODEOWNERS');

        $fs->dumpFile(
            $this->getTargetDir() . '/.github/CODEOWNERS',
            sprintf(self::CONTENT_CODEOWNERS, $owner)
        );
    }

    private function getTargetName(): string
    {
        $name = $this->getTargetRepository()->getName();

        return null !== $this->getTargetRepository()->getVendor()
            ? "{$this->getTargetRepository()->getVendor()}/{$name}"
            : $name;
    }
}
