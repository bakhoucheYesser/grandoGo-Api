<?php

$header = <<<EOF
@author Yesser Bkhouch <yesserbakhouch@hotmail.com>
EOF;

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__ . '/src')
    ->name('*.php');

return (new Config())
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setRules([
        '@Symfony' => true,
        'header_comment' => [
            'header' => $header,
            'location' => 'after_open',
            'separate' => 'none',
        ],
    ]);
