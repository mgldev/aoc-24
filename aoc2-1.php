<?php

require_once __DIR__ . '/aoc-2-common.php';

$valid = 0;

foreach (file(__DIR__ . '\day2.txt') as $idx => $line) {
    $valid += (int) levelsAreSafe(explode(' ', $line));
}

echo $valid;