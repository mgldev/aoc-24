<?php

function getTotal(string $content): int
{
        
    $pattern = '/mul\((?<left>\d{1,3}),(?<right>\d{1,3})\)/';
    $matches = [];
    preg_match_all($pattern, $content, $matches);

    $total = 0;

    for ($i = 0; $i < count($matches['left']); $i++) {
        $total += $matches['left'][$i] * $matches['right'][$i];
    }

    return $total;
}