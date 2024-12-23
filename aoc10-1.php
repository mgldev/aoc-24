<?php

class Point
{
    public function __construct(private int $x, private int $y, private int $elevation) {}

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function getElevation(): int
    {
        return $this->elevation;
    }

    public function getCoord(): array
    {
        return [$this->x, $this->y];
    }

    public function equals(Point $point): bool
    {
        return $this->x === $point->getX() && $this->y === $point->getY();
    }

    public function __toString(): string
    {
        return implode(',', array_merge($this->getCoord(), [$this->getElevation()]));
    }
}

class Path
{
    /** @var string */
    private string $uniqid;

    /** @var Point[] */
    private array $visited = [];

    /**
     * Path constructor
     *
     * @param array $visited    Initial array of visited nodes
     */
    public function __construct(array $visited = [])
    {
        $this->uniqid = uniqid('path-');

        foreach ($visited as $point) {
            $this->visited[(string) $point] = $point;
        }
    }

    /**
     * @return string
     */
    public function getUniqid(): string
    {
        return $this->uniqid;
    }

    /**
     * Get the visited nodes in this path
     *
     * @return Point[]
     */
    public function getVisited(): array
    {
        return $this->visited;
    }

    /**
     * @return int
     */
    public function getStepCount(): int
    {
        return count($this->visited);
    }

    /**
     * @return Point|null
     */
    public function latest(): ?Point
    {
        return end($this->visited);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->uniqid . ': ' . implode(' -> ', $this->visited);
    }
}

$grid = [];
$trailHeads = [];

foreach (file(__DIR__ . '/day10.txt', FILE_IGNORE_NEW_LINES) as $y => $line) {
    foreach (str_split($line) as $x => $char) {
        $point = new Point((int) $x, (int) $y, (int) $char);
        $grid[$x][$y] = $point;
        
        if ($point->getElevation() === 0) {
            $trailHeads[] = $point;
        }
    }
}

$successfulPaths = [];

foreach ($trailHeads as $trailHead) {
    $queue = new SplQueue();
    $path = new Path([$trailHead]);
    $queue->enqueue($path);
    $visited = [(string) $trailHead];

    while ($queue->count() > 0) {
        /** @var Path $path */
        $path = $queue->dequeue();
        $node = $path->latest();
    
        if ($node->getElevation() === 9) {
            $successfulPaths[] = $path;
            continue;
        }
    
        $possible = [];
        $currentElevation = $node->getElevation();
    
        foreach ([[0, 1], [0,-1], [1,0], [-1,0]] as $adjustment) {
            [$ax, $ay] = $adjustment;
            $checkX = $node->getX() + $ax;
            $checkY = $node->getY() + $ay;
    
            $inGrid = ($grid[$checkX][$checkY] ?? null) !== null;
    
            if (!$inGrid) {
                continue;
            }
            
            $checkElevation = $grid[$checkX][$checkY]->getElevation();
    
            if (($checkElevation - $currentElevation) === 1) {
                $possible[] = $grid[$checkX][$checkY];
            }
        }
    
        foreach ($possible as $neighbour) {
            if (!in_array((string) $neighbour, $visited)) {
                $visited[] = (string) $neighbour;
                $newPathVisits = $path->getVisited();
                $newPathVisits[] = $neighbour;
                $newPath = new Path($newPathVisits);
                $queue->enqueue($newPath);
            }
        }
    }
}

$successes = [];
foreach ($successfulPaths as $path) {
    $successes[] = (string) $path;
}

$answer = count(array_unique($successes));

echo $answer . "\n";