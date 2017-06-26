#!/usr/bin/env php
<?php

require_once __DIR__.'/../vendor/autoload.php';

use RocketScience\LaunchLogProcessor;

function get_microtime(): int
{
    list($microPart, $secs) = explode(' ', microtime(false));

    return intval($secs.mb_substr($microPart, 2, 6));
}

$processor = new LaunchLogProcessor();
//$stream = fopen('http://planet4589.org/space/log/launchlog.txt', 'r');
$stream = fopen(__DIR__.'/../var/data/launchlog.txt', 'r');

$start = get_microtime();

$result = $processor->groupBy($stream, 'year', null);

$stop = get_microtime();

foreach ($result as $year => $launches) {
    echo sprintf('%d : %d%s', $year, $launches, PHP_EOL);
}

echo PHP_EOL;

echo sprintf('Time: %d μs%s', ($stop - $start), PHP_EOL);
echo sprintf('Memory peak: %d KiB%s', intval(memory_get_peak_usage() / 1024), PHP_EOL);
