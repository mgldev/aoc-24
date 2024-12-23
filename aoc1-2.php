<?php

$left = $right = [];

foreach (file(__DIR__ . '\day1.txt') as $idx => $line) {
    [$l, $r] = explode('   ', $line);
    $left[] = (int) $l;
    $right[] = (int) $r;
}

$rhmap = array_count_values($right);
$sum = 0;

$types = [];

foreach ($left as $lhvalue) {
    $sum += ($lhvalue * ($rhmap[$lhvalue] ?? 0));
}

echo $sum;