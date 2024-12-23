<?php

require_once __DIR__ . '/aoc3-common.php';

function getPositionsOfWord(string $content, string $word): Generator
{
    $previousPosition = 0;

    while (($position = strpos($content, $word, $previousPosition)) !== false) {
        yield $position;
        $previousPosition = $position + 1;
    }
}

function getNextPosition(int $start, array $positions): ?int
{
    foreach ($positions as $position) {
        if ($position > $start) {
            return $position;
        }
    }

    return null;
}

function getEnabledInstructions(string $content): string
{
    $chars = str_split($content);
    $write = true;
    $donts = iterator_to_array(getPositionsOfWord($content, 'don\'t()'));
    $dos = iterator_to_array(getPositionsOfWord($content, 'do()'));
    $nextDont = getNextPosition(0, $donts);
    $nextDo = null;

    for ($i = 0; $i < count($chars); $i++) {
        if ($i === $nextDont) {
            $write = false;
            $nextDo = getNextPosition($i, $dos);
        } else if ($i === $nextDo) {
            $write = true;
            $nextDont = getNextPosition($i, $donts);
        }

        if ($write) {
            $keep[] = $chars[$i];
        }
    }

    return implode($keep);
}

$total = getTotal(getEnabledInstructions(file_get_contents(__DIR__ . '/day3.txt')));

echo $total;