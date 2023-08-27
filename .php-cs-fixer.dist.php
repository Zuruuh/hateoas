<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
    ->ignoreVCSIgnored(true)
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        '@PSR12' => true,
        '@PSR12:risky' => true,
        '@PHP81Migration' => true,
        '@PHP80Migration:risky' => true,
        'single_line_empty_body' => true,
        'ordered_imports' => true,
        '@PhpCsFixer' => true,
        'global_namespace_import' => [
            'import_classes' => true,
        ],
    ])
    ->setFinder($finder)
;
