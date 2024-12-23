<?php

$tests = [];

foreach (file(__DIR__ . '/day7.txt', FILE_IGNORE_NEW_LINES) as $line) {
    $pattern = '/(?<test>\d+): (?<numbers>.+)/';
    $matches = [];
    preg_match_all($pattern, $line, $matches);
    $testValue = (int) $matches['test'][0];
    $numbers = array_map('intval', explode(' ', $matches['numbers'][0]));
    $tests[$testValue] = $numbers;
}

/**
 * For a given array of numbers, calculate the possible operator combinations to try
 * 
 * Here we create binary combinations where the number of "bits" is equivilent to the 
 * count of numbers - 1, i.e.
 * 
 * $numbers = [1,2,3]; // count = 3
 * $bits = $count - 1 (3 - 1) = 2
 * 
 * Then work out the binary combinations:
 * - 00
 * - 11
 * - 01
 * - 10
 * 
 * Then swap 1s and 0s for 'mul' and 'add' instructions:
 * 
 * - ['add', 'add']
 * - ['mul', 'mul']
 * - ['add', 'mul']
 * - ['mul', 'add']
 */
function getOperatorCombinations(array $numbers): array
{
    $operators = [];
    $bits = count($numbers) - 1;
    $combinations = pow(2, $bits);

    for ($i = 0; $i < $combinations; $i++) {
        $binary = str_pad(decbin($i), $bits, '0', STR_PAD_LEFT);
        $operators[] = array_map(fn ($bit) => $bit ? 'mul' : 'add', str_split($binary));
    }

    return $operators;
}

$total = 0;

foreach ($tests as $expected => $numbers) {
    $combinations = getOperatorCombinations($numbers);

    foreach ($combinations as $combination) {
        foreach ($numbers as $index => $number) {
            if ($index === 0) {
                $sum = $number;
            } else {
                $operator = array_pop($combination);

                switch ($operator) {
                    case 'mul':
                        $sum *= $number;
                        break;

                    case 'add':
                        $sum += $number;
                        break;
                }
            }
        }

        if ($sum === $expected) {
            $total += $sum;
            // don't process any more combinations
            break;
        }
    }
}

echo $total . "\n";