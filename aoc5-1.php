<?php

require_once 'aoc5-common.php';

class Part1 extends PageSorter
{
    /**
     * @return int
     */
    public function answer(): int
    {
        return array_sum(
            array_map(
                fn($update) => $update[floor(count($update) / 2)],
                $this->getInvalidUpdateBreakdown()['valid']
            )
        );
    }
}

echo (new Part1)->answer() . "\n";
