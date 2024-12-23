<?php

enum Direction
{
    case LEFT;
    case RIGHT;
}

class Solution
{
    private array $grid = [];
    private int $gridX;
    private int $gridY;

    public function __construct()
    {
        foreach (file(__DIR__ . '/day8.txt', FILE_IGNORE_NEW_LINES) as $y => $line) {
            foreach (str_split($line) as $x => $char) {
                $this->grid[$x][$y] = $char;
            }
        }

        $this->gridX = count($this->grid);
        $this->gridY = count($this->grid[0]);
    }

    private function calculateAntinode(array $p1, array $p2, Direction $direction)
    {
        $deltaX = $p2[0] - $p1[0];
        $deltaY = $p2[1] - $p1[1];

        if ($direction === Direction::LEFT) {
            $x3 = $p1[0] - $deltaX;
            $y3 = $p1[1] - $deltaY;
        } else {
            $x3 = $p2[0] + $deltaX;
            $y3 = $p2[1] + $deltaY;
        }

        return [$x3, $y3];
    }
    
    private function getDistinctLines(array $points): array
    {
        $connections = [];

        foreach ($points as $point) {
            foreach ($points as $pointB) {
                $pointAS = implode(',', $point);
                $pointBS = implode(',', $pointB);
                if ($pointAS === $pointBS) continue;
                $connection = $pointAS . ':' . $pointBS;
                $connectionFlipped = $pointBS . ':' . $pointAS;
                if (!(isset($connections[$connection]) || isset($connections[$connectionFlipped]))) {
                    $connections[$connection] = true;
                }
            }
        }

        return array_keys($connections);
    }

    private function getAntennaNodes(): array
    {
        $antennai = [];

        for ($x = 0; $x < $this->gridX; $x++) {
            for ($y = 0; $y < $this->gridY; $y++) {
                $cell = $this->grid[$x][$y];
                if ($cell !== '.') {
                    if (!isset($antennai[$cell])) {
                        $antennai[$cell] = [];
                    }
                    $antennai[$cell][] = [$x, $y];
                }
            }
        }

        return $antennai;
    }

    function pointWithinGrid(array $point): bool
    {
        return ($point[0] >= 0 && $point[0] < $this->gridX) && ($point[1] >= 0 && $point[1] < $this->gridY);
    }

    public function solve(): int
    {
        $antinodes = [];

        foreach ($this->getAntennaNodes() as $antennaKey => $points) {
            foreach ($this->getDistinctLines($points) as $line) {
                [$lineStart, $lineEnd] = array_map(fn ($coord) => explode(',', $coord), explode(':', $line));
                $antinodes[implode(',', $lineStart)] = true;
                $antinodes[implode(',', $lineEnd)] = true;

                foreach ([Direction::LEFT, Direction::RIGHT] as $direction) {
                    $a = $lineStart;
                    $b = $lineEnd;
                    $c = $this->calculateAntinode($a, $b, $direction);

                    while ($this->pointWithinGrid($c)) {
                        $antinodes[implode(',', $c)] = true;

                        if ($direction === Direction::LEFT) {
                            $b = $a;
                            $a = $c;
                        } else {
                            $a = $b;
                            $b = $c;
                        }

                        $c = $this->calculateAntinode($a, $b, $direction);
                    }
                }
            }
        }

        return count($antinodes);
    }
}

echo (new Solution)->solve() . "\n";