<?php

$grid = [];

foreach (file(__DIR__ . '/day4.txt', FILE_IGNORE_NEW_LINES) as $y => $line) {
    foreach (str_split($line) as $x => $char) {
        $grid[$x][$y] = $char;
    }
}

$xMax = count($grid);
$yMax = count($grid[1]);

function masExists(array $chars): bool
{
    $word = implode($chars);
    
    return in_array('MAS', [$word, strrev($word)]);
}

$xmasCount = 0;
$masses = [];

for ($y = 0; $y < $yMax; $y++) {
    for ($x = 0; $x < $xMax; $x++) {
         // diagonal LTR
         if (($x + 3) <= $xMax && ($y + 3) <= $yMax) {
            $word = [];
            $coords = [];
            for ($i = 0; $i < 3; $i++) {
                $word[] = $grid[$x + $i][$y + $i];
                $coords[] = [$x + $i, $y + $i];
            }
            if (masExists($word)) {
                // store the center co-ordinate of the MAS (the A), i.e. 2,2
                $masses[] = implode(',', $coords[1]);
            }
         }

         // diagonal RTL
         if (($x - 2) >= 0 && ($y + 3) <= $yMax) {
            $word = [];
            $coords = [];
            for ($i = 0; $i < 3; $i++) {
                $coords[] = [$x - $i, $y + $i];
                $word[] = $grid[$x - $i][$y + $i];
            }
  
            if (masExists($word)) {
                // store the center co-ordinate of the MAS (the A), i.e. 2,2
                $masses[] =  implode(',', $coords[1]);
            }
         }
    }
}

// if more than 1 entry exists for a given A in MAS, it can only be in a "star" formation
$answer = count(array_filter(array_count_values($masses), fn (int $count) => $count > 1));

echo $answer . "\n";