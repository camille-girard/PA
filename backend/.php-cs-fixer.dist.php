<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'yoda_style' => true,
        'phpdoc_to_comment' => [
            'ignored_tags' => ['var']
        ],
    ])
    ->setFinder($finder)
;
