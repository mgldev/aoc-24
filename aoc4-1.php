<?php

$grid = [];

foreach (file(__DIR__ . '/day4.txt', FILE_IGNORE_NEW_LINES) as $y => $line) {
    foreach (str_split($line) as $x => $char) {
        $grid[$x][$y] = $char;
    }
}

$xMax = count($grid);
$yMax = count($grid[1]);

function xmasExists(array $chars): bool
{
    $word = implode($chars);
    
    return in_array('XMAS', [$word, strrev($word)]);
}

$xmasCount = 0;

for ($y = 0; $y < $yMax; $y++) {
    for ($x = 0; $x < $xMax; $x++) {
         // diagonal LTR
         if (($x + 4) <= $xMax && ($y + 4) <= $yMax) {
            $word = [];
            for ($i = 0; $i < 4; $i++) {
                $word[] = $grid[$x + $i][$y + $i];
            }
            $xmasCount += (int) xmasExists($word);
         }

         // diagonal RTL
         if (($x - 3) >= 0 && ($y + 4) <= $yMax) {
            $word = [];
            for ($i = 0; $i < 4; $i++) {
                $coords[] = [$x - $i, $y + $i];
                $word[] = $grid[$x - $i][$y + $i];
            }
  
            $xmasCount += (int) xmasExists($word);
         }

         // horizontal
         if (($x + 4) <= $xMax) {
            $word = [];
            for ($i = 0; $i < 4; $i++) {
                $word[] = $grid[$x + $i][$y];
            }
            $xmasCount += (int) xmasExists($word);
         }

        // vertical
        if (($y + 4) <= $yMax) {
            $word = [];
            for ($i = 0; $i < 4; $i++) {
                $word[] = $grid[$x][$y + $i];
            }
            $xmasCount += (int) xmasExists($word);
        }
    }
}

echo $xmasCount . "\n";