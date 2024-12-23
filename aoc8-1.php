<?php

$grid = [];

foreach (file(__DIR__ . '/day8.txt', FILE_IGNORE_NEW_LINES) as $y => $line) {
    foreach (str_split($line) as $x => $char) {
        $grid[$x][$y] = $char;
    }
}

$debug = $grid;

// determine which direction we want an antinode for
enum Direction
{
    case LEFT;
    case RIGHT;
}

/**
 * For two given points (antennai), and a given direction (L or R), calculate where
 * an antinode should be positioned
 */
function calculateAntinode(array $p1, array $p2, Direction $direction) 
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

/**
 * For a given set of points, calculate the distinct lines / connections which can be made between them
 * 
 * i.e.
 * 
 * A        B
 *      C
 * 
 * A -> B
 * B -> C
 * C -> A
 * 
 * or (function output)
 * 
 * 0,0         4,0
 *       2,1
 * 
 * [
 *      '0,0:2,1',
 *      '2,1:4,0',
 *      '4,0:0,0'
 * ]
 * 
 * We need this as in order to be able to compute the position of an antinode we
 * need a "line", and a line can only be formed from 2 or more points
 */
function getDistinctLines(array $points)
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

/**
 * Find all antennai within the grid, keyed by the antenna name (frequency), with
 * the value being all points (coords) present for that frequency
 */
function getAntennaNodes(array $grid): array
{
    $antennai = [];

    for ($x = 0; $x < count($grid); $x++) {
        for ($y = 0; $y < count($grid[$x]); $y++) {
            $cell = $grid[$x][$y];
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

/**
 * Ensure a given point is within the bounds of the grid
 */
function pointWithinGrid(array $point, array $grid): bool
{
    $gridX = count($grid);
    $gridY = count($grid[0]);

    return ($point[0] >= 0 && $point[0] < $gridX) && ($point[1] >= 0 && $point[1] < $gridY);
}

$antinodes = [];

// for each antenna frequency in the grid, get the antenna locations ($points)
foreach (getAntennaNodes($grid) as $antennaKey => $points) {

    // find each unique line between each antenna
    $lines = getDistinctLines($points);

    // for each line, calculate the antinode to add at either end of the line
    foreach ($lines as $line) {
        $coords = explode(':', $line);
        $a = explode(',', $coords[0]);
        $b = explode(',', $coords[1]);

        $lineAntinodes = array_map(
            fn (Direction $direction) => calculateAntinode($a, $b, $direction),
            [Direction::LEFT, Direction::RIGHT]
        );

        // if the antinode is within the grid boundaries, register it (using array key prevents dupes)
        foreach ($lineAntinodes as $antinode) {
            if (pointWithinGrid($antinode, $grid)) {
                $antinodes[implode(',', $antinode)] = true;
            }
        }
    }
}

// answer is number of unique antinodes
echo count($antinodes) . "\n";