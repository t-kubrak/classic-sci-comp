<?php

require_once 'generic_search.php';

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
    }

    private function createGrid(int $rows, int $columns, int $sparseness): ArrayObject
    {
        $grid = $this->createEmptyGrid($rows, $columns);
        $grid = $this->randomlyFillGrid($grid, $sparseness);

        return $grid;
    }

    private function createEmptyGrid(int $rows, int $columns): ArrayObject
    {
        $grid = new Sequence();

        for ($row = 1; $row <= $rows; $row++) {
            $cellsRow = new Sequence();

            for ($column = 1; $column <= $columns; $column++) {
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
                if (random_int(1, 100) < $sparseness) {
                    $grid[$rowKey][$columnKey] = Cell::BLOCKED;
                }
            }
        }

        return $grid;
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
}

$maze = new Maze();

echo $maze;