<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/cli')
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests')
;

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        'strict_param' => true,
    ])
    ->setFinder($finder)
;
