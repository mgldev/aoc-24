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

$guard = [
    'direction' => Direction::UP,
    'position' => $startingPosition = getStartingCoordinate($grid)
];

$distinctVisits = [implode(',', $startingPosition)];

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

$nextPosition = getNextCoordinate($guard);

// show's over when we've gone out of bounds of the grid
while ($nextPosition[0] >=0 && $nextPosition[0] < $xMax && $nextPosition[1] >= 0 && $nextPosition[1] < $yMax) {
    [$x, $y] = $nextPosition;

    if ($grid[$x][$y] === '#') {
        // only turn the guard, don't move her
        $guard['direction'] = $guard['direction']->next();
    } else {
        // set the guard's current position to the determined position (because she wasn't blocked)
        $guard['position'] = $nextPosition;

        // log the new position (because we've moved)
        $distinctVisits[] = implode(',', $nextPosition);
    }

    // determine the next position
    $nextPosition = getNextCoordinate($guard);
}

echo count(array_unique($distinctVisits)) . "\n";
