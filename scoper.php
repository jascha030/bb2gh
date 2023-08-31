<?php

/**
 * @noinspection PhpUndefinedNamespaceInspection
 * @noinspection PhpUndefinedClassInspection
 */

declare(strict_types=1);

use Isolated\Symfony\Component\Finder\Finder;

return (static function (): array {
    $create = Finder::create(...);

    return [
        'prefix'  => 'Bitbucket2Github',
        'finders' => [
            $create()->files()->in('src'),
            $create()->files()->in('vendor')
                ->ignoreVCS(true)
                ->notName('/LICENSE|.*\\.md|.*\\.dist|Makefile|composer\\.json|composer\\.lock/')
                ->exclude(['doc', 'test', 'test_old', 'tests', 'Tests']),
            $create()->append(['composer.json']),
        ],
        'expose-global-constants' => true,
        'expose-global-classes'   => true,
        'expose-global-functions' => true,
    ];
})();
