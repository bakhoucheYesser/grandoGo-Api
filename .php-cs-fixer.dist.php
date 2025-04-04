<?php

$header = <<<EOF
This file is part of the GrandoGo project.

(c) Yesser Bkhouch <yesserbakhouch@hotmail.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests'
    ])
    ->name('*.php')
    ->notName('*Kernel.php')
    ->exclude([
        'var',
        'vendor',
        'public'
    ]);

return (new Config())
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setIndent('    ')
    ->setLineEnding("\n")
    ->setRules([
        '@Symfony' => true,
        '@PHP80Migration:risky' => true,
        'header_comment' => [
            'header' => $header,
            'location' => 'after_open',
            'separate' => 'both',
        ],
        'strict_param' => true,
        'declare_strict_types' => true,
        'no_unused_imports' => true,
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
        ],
        'phpdoc_trim' => true,
        'no_extra_blank_lines' => true,
        'trailing_comma_in_multiline' => true,
        'array_syntax' => ['syntax' => 'short'],
        'single_quote' => true,
        'native_function_invocation' => true,
    ]);