<?php

$left = $right = [];

foreach (file(__DIR__ . '\day1.txt') as $idx => $line) {
    [$l, $r] = explode('   ', $line);
    $left[] = (int) $l;
    $right[] = (int) $r;
}

sort($left);
sort($right);

$sum = 0;
for ($i = 0; $i < count($left); $i++) {
    $sum += abs($left[$i] - $right[$i]);
}

echo $sum;