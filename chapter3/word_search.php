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

    /**
     * @return int
     */
    public function getRow(): int
    {
        return $this->row;
    }

    /**
     * @return int
     */
    public function getColumn(): int
    {
        return $this->column;
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
        echo implode("", $row)."\n";
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

class WordSearchConstraint extends Constraint
{
    /**
     * @var ArrayObject
     */
    private ArrayObject $words;

    public function __construct(ArrayObject $words)
    {
        parent::__construct($words);

        $this->words = $words;
    }

    public function satisfied(Map $assignment): bool
    {
        $allLocations = [];

        foreach ($assignment->getIterator() as $values) {
            foreach ($values as $location) {
                $allLocations[] = $location;
            }
        }

        $set = new \Ds\Set($allLocations);

        return $set->count() == count($allLocations);
    }
}

$grid = generateGrid(9, 9);
$words = new Sequence(["MATTHEW", "JOE", "MARY", "SARAH", "SALLY"]);
$locations = TypedMap::forType(TypedSequence::class);

foreach ($words as $word) {
    $locations[$word] = generateDomain($word, $grid);
}

$csp = new CSP($words, $locations);
$csp->addConstraint(new WordSearchConstraint($words));

$solution = $csp->backtrackingSearch();
if (!$solution) {
    echo "No solution found.";
} else {
    /** @var GridLocation[]  $gridLocations */
    foreach ($solution as $word => $gridLocations) {
        if (rand(0, 1)) {
            $gridLocations = array_reverse($gridLocations);
        }


        foreach (str_split($word) as $index => $letter) {
            $row = $gridLocations[$index]->getRow();
            $column = $gridLocations[$index]->getColumn();
            $grid[$row][$column] = $letter;
        }

        displayGrid($grid);
    }
}