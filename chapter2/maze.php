<?php

require_once 'generic_search.php';
require_once '../data_structures.php';

class Cell
{
    public const EMPTY = ' ';
    public const BLOCKED = 'X';
    public const START = 'S';
    public const GOAL = 'G';
    public const PATH = '*';
}

class MazeLocation
{
    /**
     * @var int
     */
    private int $row;

    /**
     * @var int
     */
    private int $column;

    public function __construct(int $row, int $column)
    {
        $this->row = $row;
        $this->column = $column;
    }

    public function row(): int
    {
        return $this->row;
    }

    public function column(): int
    {
        return $this->column;
    }

    public function upper(): self
    {
        $location = clone $this;
        $location->row += 1;

        return $location;
    }

    public function lower(): self
    {
        $location = clone $this;
        $location->row -= 1;

        return $location;
    }

    public function toTheRight(): self
    {
        $location = clone $this;
        $location->column += 1;

        return $location;
    }

    public function toTheLeft(): self
    {
        $location = clone $this;
        $location->column -= 1;

        return $location;
    }
}

class Maze
{
    /**
     * @var int
     */
    private int $rows;
    /**
     * @var int
     */
    private int $columns;
    /**
     * @var int
     */
    private int $sparseness;
    /**
     * @var MazeLocation
     */
    private MazeLocation $start;
    /**
     * @var MazeLocation
     */
    private MazeLocation $goal;

    /**
     * @var ArrayObject
     */
    private ArrayObject $grid;

    public function __construct(
        int $rows = 10,
        int $columns = 10,
        int $sparseness = 20,
        MazeLocation $start = null,
        MazeLocation $goal = null
    )
    {
        $this->rows = $rows;
        $this->columns = $columns;
        $this->sparseness = $sparseness;
        $this->start = $start ?? new MazeLocation(0, 0);
        $this->goal = $goal ?? new MazeLocation(9, 9);
        $this->grid = $this->createGrid($rows, $columns, $sparseness);

        $this->grid[$this->start->row()][$this->start->column()] = Cell::START;
        $this->grid[$this->goal->row()][$this->goal->column()] = Cell::GOAL;
    }

    private function createGrid(int $rows, int $columns, int $sparseness): ArrayObject
    {
        $grid = $this->createEmptyGrid($rows, $columns);
        $grid = $this->randomlyFillGrid($grid, $sparseness);

        return $grid;
    }

    private function createEmptyGrid(int $rows, int $columns): ArrayObject
    {
        $grid = new ArrayObject();

        for ($row = 0; $row < $rows; $row++) {
            $cellsRow = new ArrayObject();

            for ($column = 0; $column < $columns; $column++) {
                $cellsRow[$column] = Cell::EMPTY;
            }

            $grid[$row] = $cellsRow;
        }

        return $grid;
    }

    private function randomlyFillGrid(ArrayObject $emptyGrid, int $sparseness): ArrayObject
    {
        $grid = clone $emptyGrid;

        foreach ($grid as $rowKey => $row) {
            foreach ($row as $columnKey => $column) {
                if (random_int(0, 100) < $sparseness) {
                    $grid[$rowKey][$columnKey] = Cell::BLOCKED;
                }
            }
        }

        return $grid;
    }

    /**
     * @return MazeLocation
     */
    public function getStart(): MazeLocation
    {
        return $this->start;
    }

    /**
     * @return MazeLocation
     */
    public function getGoal(): MazeLocation
    {
        return $this->goal;
    }

    public function __toString(): string
    {
        $output = "";

        foreach ($this->grid as $row) {
            foreach ($row as $columnValue) {
                $output .= $columnValue;
            }

            $output .= "\n";
        }

        return $output;
    }

    public function goalTest(MazeLocation $location): bool
    {
        return $location == $this->goal;
    }

    public function successors(MazeLocation $location): TypedSequence
    {
        $locations = TypedSequence::forType(MazeLocation::class);

        $upperLocation = $location->upper();
        $lowerLocation = $location->lower();
        $rightLocation = $location->toTheRight();
        $leftLocation = $location->toTheLeft();

        if ($upperLocation->row() < $this->rows
            && $this->grid[$upperLocation->row()][$upperLocation->column()] != Cell::BLOCKED
        ) {
            $locations->append($upperLocation);
        }

        if ($lowerLocation->row() >= 0
            && $this->grid[$lowerLocation->row()][$lowerLocation->column()] != Cell::BLOCKED
        ) {
            $locations->append($lowerLocation);
        }

        if ($rightLocation->column() < $this->columns
            && $this->grid[$rightLocation->row()][$rightLocation->column()] != Cell::BLOCKED
        ) {
            $locations->append($rightLocation);
        }

        if ($leftLocation->column() >= 0
            && $this->grid[$leftLocation->row()][$leftLocation->column()] != Cell::BLOCKED
        ) {
            $locations->append($leftLocation);
        }

        return $locations;
    }

    /**
     * @param MazeLocation[] $path
     */
    public function mark(array $path): void
    {
        foreach ($path as $mazeLocation) {
            $this->grid[$mazeLocation->row()][$mazeLocation->column()] = Cell::PATH;
        }

        $this->grid[$this->start->row()][$this->start->column()] = Cell::START;
        $this->grid[$this->goal->row()][$this->goal->column()] = Cell::GOAL;
    }

    /**
     * @param MazeLocation[] $path
     */
    public function clear(array $path): void
    {
        foreach ($path as $mazeLocation) {
            $this->grid[$mazeLocation->row()][$mazeLocation->column()] = Cell::EMPTY;
        }

        $this->grid[$this->start->row()][$this->start->column()] = Cell::START;
        $this->grid[$this->goal->row()][$this->goal->column()] = Cell::GOAL;
    }
}

function euclideanDistance(MazeLocation $goal): callable
{
    $distance = function (MazeLocation $ml) use ($goal): float {
        $xDist = abs($ml->column() - $goal->column());
        $yDist = abs($ml->row() - $goal->row());
        return sqrt(($xDist * $xDist) + ($yDist * $yDist));
    };

    return $distance;
}

function manhattanDistance(MazeLocation $goal): callable
{
    $distance = function (MazeLocation $ml) use ($goal): int {
        $xDist = abs($ml->column() - $goal->column());
        $yDist = abs($ml->row() - $goal->row());
        return $xDist + $yDist;
    };

    return $distance;
}

$maze = new Maze();

echo $maze . "\n";

$solution1 = dfs($maze->getStart(), [$maze, 'goalTest'], [$maze, 'successors']);

if (!$solution1) {
    echo "No solution found using depth-first search.\n";
} else {
    $path1 = nodeToPath($solution1);
    $maze->mark($path1);
    echo $maze . "\n";
    $maze->clear($path1);
}

$solution2 = bfs($maze->getStart(), [$maze, 'goalTest'], [$maze, 'successors']);

if (!$solution2) {
    echo "No solution found using breadth-first search.\n";
} else {
    $path2 = nodeToPath($solution2);
    $maze->mark($path2);
    echo $maze . "\n";
    $maze->clear($path2);
}

$distance = manhattanDistance($maze->getGoal());

$solution3 = astar($maze->getStart(), [$maze, 'goalTest'], [$maze, 'successors'], $distance);

if (!$solution3) {
    echo "No solution found using A*.\n";
} else {
    $path3 = nodeToPath($solution3);
    $maze->mark($path3);
    echo $maze . "\n";
    $maze->clear($path3);
}
