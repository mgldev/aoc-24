<?php

$grid = [];

foreach (file(__DIR__ . '/day6.txt', FILE_IGNORE_NEW_LINES) as $y => $line) {
    foreach (str_split($line) as $x => $char) {
        $grid[$x][$y] = $char;
    }
}

$xMax = count($grid);
$yMax = count($grid[1]);

enum Direction
{
    case UP;
    case RIGHT;
    case DOWN;
    case LEFT;

    public function next(): Direction
    {
        return match($this) {
            Direction::UP => Direction::RIGHT,
            Direction::RIGHT => Direction::DOWN,
            Direction::DOWN => Direction::LEFT,
            Direction::LEFT => Direction::UP
        };
    }
}

function getStartingCoordinate(array $grid): ?array
{
    for ($x = 0; $x < count($grid); $x++) {
        if (($start = array_search('^', $grid[$x])) !== false) {
            return [$x, $start];
        }
    }

    return null;
}

function getNextCoordinate(array $guard): array
{
    [$x, $y] = $guard['position'];

    switch ($guard['direction']) {
        case Direction::UP:
            $y--;
            break;
        case Direction::RIGHT:
            $x++;
            break;
        case Direction::DOWN:
            $y++;
            break;
        case Direction::LEFT:
            $x--;
            break;
    }

    return [$x, $y];
}

$loops = 0;

// brute force - for every position in the grid...
for ($gridX = 0; $gridX < $xMax; $gridX++) {
    for ($gridY = 0; $gridY < $yMax; $gridY++) {

        $guard = [
            'direction' => Direction::UP,
            'position' => $startingPosition = getStartingCoordinate($grid)
        ];

        $nextPosition = getNextCoordinate($guard);

        $copy = $grid;

        // if the current cell isn't already a blocker, make it one
        if ($copy[$gridX][$gridY] !== '#') {
            $copy[$gridX][$gridY] = '#';
        }

        // keep track of how many iterations ("moves") the guard has made
        $iterations = 0;

        // same logic as P1...
        while ($nextPosition[0] >=0 && $nextPosition[0] < $xMax && $nextPosition[1] >= 0 && $nextPosition[1] < $yMax) {
            // vomit: if we've been wandering around this maze having made 12,500+ moves, we're probably in a loop
            // started at 25,000 - AOC validated the answer, same answer @ 12,500, wrong answer @ 5,000
            // the lower the number, the faster the execution, but the closer you get to getting a wrong answer
            if ($iterations > 12500) {
                $loops++;
                break;
            }

            [$x, $y] = $nextPosition;

            if ($copy[$x][$y] === '#') {
                $guard['direction'] = $guard['direction']->next();
            } else {
                $guard['position'] = $nextPosition;
            }

            $nextPosition = getNextCoordinate($guard);
            $iterations++;
        }
    }
}

echo $loops;
