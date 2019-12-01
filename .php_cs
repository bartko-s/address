<?php
$finder = PhpCsFixer\Finder::create()
    ->in('config')
    ->in('src')
    ->in('tests')
    ->notPath('_files');
return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        '@Symfony' => true,
        'protected_to_private' => false,
        'declare_strict_types' => false,
        'no_superfluous_phpdoc_tags' => array(
            'allow_unused_params' => true,
        ),
        'single_line_throw' => false,
        'array_syntax' => array(
            'syntax' => 'long'
        ),
    ])
    ->setFinder($finder);