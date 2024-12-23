<?php

function levelsAreSafe(array $levels): bool
{
    $levels = array_map('intval', $levels);
    $asc = $desc = $levels;
    sort($asc);
    rsort($desc);
    $isDescending = $levels === $desc;
    $isOrdered = $levels === $asc || $levels === $desc;
    
    if (!$isOrdered) return false;

    for ($i = 0; $i < count($levels) - 1; $i++) {
        $a = $levels[$i];
        $b = $levels[$i + 1];

        if ($a === $b || abs($a - $b) > 3) return false;
    }

    return true;
}