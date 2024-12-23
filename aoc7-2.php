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

function getOperatorCombinations(int $length) 
{
    $symbols = ['MUL', 'ADD', 'CAT'];

    if ($length === 0) {
        return [[]];
    }

    $combinations = [];
    $subCombinations = getOperatorCombinations($length - 1);

    foreach ($symbols as $symbol) {
        foreach ($subCombinations as $subCombination) {
            array_unshift($subCombination, $symbol);
            $combinations[] = $subCombination;
        }
    }

    return $combinations;
}

function getInstructionSets(array $numbers): array
{
    $result = [];
    $combinations = getOperatorCombinations(count($numbers) - 1);

    foreach ($combinations as $combination) {
        $set = [];

        foreach ($numbers as $index => $number) {
            if ($index === 0) {
                $set[] = $number;
            } else {
                $operator = array_pop($combination);
                $set[] = $operator;
                $set[] = $number;
            }
        }

        $result[] = $set;
    }

    return $result;
}

$total = 0;

foreach ($tests as $expected => $numbers) {
    $instructionSets = getInstructionSets($numbers);

    foreach ($instructionSets as $instructionSet) {
        $sum = 0;
        
        for ($i = 0; $i < count($instructionSet); $i++) {
            if ($i === 0) {
                $sum = (int) $instructionSet[$i];
            } else if (isset($instructionSet[$i]) && isset($instructionSet[$i + 1])) {
                $operator = $instructionSet[$i];
                $value = $instructionSet[$i + 1];
                
                switch ($operator) {
                    case 'MUL':
                        $sum *= $value;
                        break;
                    case 'ADD':
                        $sum += $value;
                        break;
                    case 'CAT':
                        $sum = (int) ($sum . $value);
                        break;
                }

                $i + 1;
            }
        }

        if ($sum === $expected) {
            $total += $sum;
            break;
        }
    }
}

echo $total . "\n";