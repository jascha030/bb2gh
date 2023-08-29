<?php

/**
 * @noinspection PhpUndefinedNamespaceInspection
 * @noinspection PhpUndefinedClassInspection
 */

declare(strict_types=1);

use Isolated\Symfony\Component\Finder\Finder;

return [
    'finders' => [
        Finder::create()->files()->in('src'),
        Finder::create()
            ->files()
            ->ignoreVCS(true)
            ->notName('/LICENSE|.*\\.md|.*\\.dist|Makefile|composer\\.json|composer\\.lock/')
            ->exclude(['doc', 'test', 'test_old', 'tests', 'Tests'])
            ->in('vendor'),
        Finder::create()->append(['composer.json']),
    ],
    'prefix' => 'Bitbucket2Github',
    'expose-global-constants' => true,
    'expose-global-classes'   => true,
    'expose-global-functions' => true,
];
