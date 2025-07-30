<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'single_line_throw' => false,
        'concat_space' => ['spacing' => 'one'],
        'no_multiline_whitespace_around_double_arrow' => false,
        'phpdoc_align' => false,
        'visibility_required' => false,
        'types_spaces' => false,
        'global_namespace_import' => false,
        'class_definition' => false,
        'yoda_style' => false,
    ])
    ->setFinder($finder)
;
