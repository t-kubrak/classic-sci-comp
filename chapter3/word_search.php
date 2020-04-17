<?php

require_once "csp.php";
require_once "../data_structures.php";

class GridLocation
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

function generateGrid(int $rows, int $columns):array
{
    $characters = range('A', 'Z');
    $grid = [];

    foreach (range(0, $rows) as $row) {
        foreach (range(0, $columns) as $column) {
            $grid[$row][$column] = $characters[array_rand($characters)];
        }
    }

    return $grid;
}

function displayGrid(array $grid): void
{
    foreach ($grid as $row) {
        echo implode("", $row);
    }
}
