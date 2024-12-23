<?php

require_once __DIR__ . '/aoc-2-common.php';

$valid = 0;

foreach (file(__DIR__ . '\day2.txt') as $idx => $line) {
    $levels = explode(' ', $line);
    
    if (levelsAreSafe($levels)) {
        $valid++;
        continue;
    }

    for ($i = 0; $i < count($levels); $i++) {
        $test = $levels;
        unset($test[$i]);
        $test = array_values($test);

        if (levelsAreSafe($test)) {
            $valid++;
            continue 2;
        }
    }
}

echo $valid;