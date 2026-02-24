<?php

declare(strict_types=1);

use Jascha030\PhpCsFixer\Config;
use PhpCsFixer\Finder;

require_once __DIR__ . '/vendor-bin/php-cs-fixer/vendor/autoload.php';

/**
 * Cache dir and file location.
 */
$cacheDirectory = __DIR__ . '/.var/cache';

/**
 * Create a .cache dir if not already present.
 */
if (! file_exists($cacheDirectory) && ! mkdir($cacheDirectory, 0o700, true) && ! is_dir($cacheDirectory)) {
    throw new RuntimeException(sprintf('Directory "%s" was not created', $cacheDirectory));
}

$finder = Finder::create()
    ->in(__DIR__)
    ->exclude([
        '.github',
        '.phive',
        '.var',
        'tools',
        'vendor',
        'vendor-bin',
    ])
    ->name(['/\.?.*.php/'])
    ->ignoreDotFiles(false);

return (new Config(
    Config::PHP_83,
    null,
))
    ->setFinder($finder)
    ->setCacheFile("{$cacheDirectory}/.php-cs-fixer.cache");
