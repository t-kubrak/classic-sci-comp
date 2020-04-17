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

function generateGrid(int $rows, int $columns): array
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

function generateDomain(string $word, array $grid)
{
    $domain = TypedSequence::forType('array');
    $height = count($grid);
    $width = count($grid[0]);
    $length = strlen($word);

    foreach (range(0, $height) as $row) {
        foreach (range(0, $width) as $col) {
            $columns = range($col, $col + $length + 1);
            $rows = range($row, $row + $length + 1);

            if ($col + $length <= $width) {
                /** left to right */
                $domain->append(
                    array_map(function ($c) use ($row) {
                        return new GridLocation($row, $c);
                    }, $columns)
                );

                /** diagonal towards bottom right */
                if ($row + $length <= $height) {
                    $domain->append(
                        array_map(function ($r) use ($row, $col) {
                            return new GridLocation($r, $col + ($r - $row));
                        }, $rows)
                    );
                }
            }

            if ($row + $length <= $height) {
                /** top to bottom */
                $domain->append(
                    array_map(function ($r) use ($col) {
                        return new GridLocation($r, $col);
                    }, $rows)
                );

                /** diagonal towards bottom left */
                if ($col - $length >= 0) {
                    $domain->append(
                        array_map(function ($r) use ($row, $col) {
                            return new GridLocation($r, $col - ($r - $row));
                        }, $rows)
                    );
                }
            }
        }
    }

    return $domain;
}
