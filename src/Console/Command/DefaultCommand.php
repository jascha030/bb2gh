<?php

declare(strict_types=1);

namespace Jascha030\Bitbucket2Github\Console\Command;

use InvalidArgumentException;
use Jascha030\Bitbucket2Github\Executable\Executable;
use Jascha030\Bitbucket2Github\Executable\Git;
use Jascha030\Bitbucket2Github\Repository\ComposerRepository;
use Jascha030\Bitbucket2Github\Repository\RepositoryInterface;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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

    private RepositoryInterface $repo;

    private Git $git;

    private Executable $gh;

    /**
     * @throws \Seld\JsonLint\ParsingException
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $directory = $input->getArgument('directory');

        $this->targetDir = match (true) {
            ! is_string($directory) => throw new InvalidArgumentException('Directory argument must be a string.'),
            ! is_dir($directory)    => throw new InvalidArgumentException('Directory does not exist.'),
            default                 => $directory
        };

        $this->validateOrigin();

        if (Command::FAILURE === $this->removeOrigin()->mustRun()->getExitCode()) {
            throw new RuntimeException('Could not remove origin remote');
        }

        if (Command::FAILURE === $this->createRepo()->mustRun()->getExitCode()) {
            throw new RuntimeException('Could not create repository');
        }

        $owner = $input->getOption('owner');

        if (is_string($owner)) {
            $this->createGithubFiles($owner);
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
            ->addArgument(
                'organization',
                InputArgument::OPTIONAL,
                'The organization name',
                'WP-Brothers'
            )
            ->addOption(
                '--owner',
                '-o',
                InputArgument::OPTIONAL,
                'The owner of the repository.'
            );
    }

    private function createGithubFiles(string $owner): void
    {
        if (! str_starts_with($owner, '@')) {
            $owner = '@' . $owner;
        }

        $fs = new Filesystem();

        if ($fs->exists($this->targetDir . '/.github/CODEOWNERS')) {
            return;
        }

        $fs->mkdir($this->targetDir . '/.github');
        $fs->touch($this->targetDir . '/.github/CODEOWNERS');

        $fs->dumpFile(
            $this->targetDir . '/.github/CODEOWNERS',
            sprintf(self::CONTENT_CODEOWNERS, $owner)
        );
    }

    /**
     * @throws \Seld\JsonLint\ParsingException
     */
    private function getRepo(): RepositoryInterface
    {
        if (! isset($this->repo)) {
            $this->repo = new ComposerRepository($this->targetDir);
        }

        return $this->repo;
    }

    private function getGit(): Git
    {
        if (! isset($this->git)) {
            $this->git = new Git();
        }

        return $this->git;
    }

    private function getGithubCLI(): Executable
    {
        if (! isset($this->gh)) {
            $this->gh = new Executable('gh');
        }

        return $this->gh;
    }

    private function validateOrigin(): void
    {
        $process = Process::fromShellCommandline($this->getGit() . ' remote -v', $this->targetDir)->mustRun();
        $lines   = array_filter(explode(PHP_EOL, $process->getOutput()), 'strlen');
        $lines   = array_filter($lines, static fn (string $line): bool => str_contains($line, 'git@github.com'));

        if (0 !== count($lines)) {
            throw new RuntimeException('Origin already contains a github remote.');
        }
    }

    private function removeOrigin(): Process
    {
        return Process::fromShellCommandline($this->getGit() . ' remote rm origin', $this->targetDir);
    }

    /**
     * @throws \Seld\JsonLint\ParsingException
     */
    private function createRepo(): Process
    {
        $argString = '';
        $args      = [
            '--source'  => $this->targetDir,
            '--remote'  => 'origin',
            '--private' => null,
            '--push'    => null,
        ];

        if (! empty($this->getRepo()->getDescription())) {
            $args['--description'] = $this->getRepo()->getDescription();
        }

        foreach ($args as $option => $value) {
            if (null === $value) {
                $argString .= $option . ' ';
            }

            if (null !== $value) {
                $argString .= $option . '="' . $value . '" ';
            }
        }

        return Process::fromShellCommandline("{$this->getGithubCLI()} repo create WP-Brothers/{$this->getRepo()->getName()} {$argString}");
    }
}
