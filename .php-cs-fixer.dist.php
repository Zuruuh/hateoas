<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->ignoreVCSIgnored(true)
    ->in(__DIR__ . '/tests')
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
    ])
    ->setFinder($finder)
;
