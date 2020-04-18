<?php

require_once "csp.php";
require_once "../data_structures.php";

class GridLocation implements \Ds\Hashable
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

    /**
     * @inheritDoc
     */
    function hash()
    {
        return $this->row . $this->column;
    }

    /**
     * @inheritDoc
     * @var GridLocation $otherLocation
     */
    function equals($otherLocation): bool
    {
        return $this->hash() == $otherLocation->hash();
    }
}

function generateGrid(int $rows, int $columns): array
{
    $grid = [];

    foreach (range(0, $rows - 1) as $row) {
        foreach (range(0, $columns - 1) as $column) {
            $grid[$row][$column] = "";
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

function generateDomain(array $chip, array $grid)
{
    $domain = TypedSequence::forType('array');
    $height = count($grid);
    $width = count($grid[0]);

//    $heightLength = count($chip);
//    $widthLength = count($chip[0]);
    $length = count($chip);

    foreach (range(0, $height - 1) as $row) {
        foreach (range(0, $width - 1) as $col) {
            $columns = range($col, $col + $length - 1);
            $rows = range($row, $row + $length - 1);

            if ($col + $length <= $width) {
                /** left to right */
                $domain->append(
                    array_map(function ($c) use ($row) {
                        return new GridLocation($row, $c);
                    }, $columns)
                );
            }

            if ($row + $length <= $height) {
                /** top to bottom */
                $domain->append(
                    array_map(function ($r) use ($col) {
                        return new GridLocation($r, $col);
                    }, $rows)
                );
            }
        }
    }

    return $domain;
}

class BoardLayoutConstraint extends Constraint
{
    /**
     * @var ArrayObject
     */
    private ArrayObject $chips;

    public function __construct(ArrayObject $chips)
    {
        parent::__construct($chips);

        $this->chips = $chips;
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

$grid = generateGrid(8, 8);
//$chip1 = [[1, 1, 1]];
//$chip2 = [2, 2, 2, 2];
//$chip3 =   [[3, 3],
//            [3, 3],
//            [3, 3]];
$chip1 = [1, 1, 1];
$chip2 = [2, 2, 2, 2];

$chips = new Sequence([$chip1, $chip2]);
$locations = TypedMap::forType(TypedSequence::class);

foreach ($chips as $chip) {
    $locations[] = generateDomain($chip, $grid);
}

$csp = new CSP($chips, $locations);
$csp->addConstraint(new BoardLayoutConstraint($chips));

$solution = $csp->backtrackingSearch();

if (!$solution) {
    echo "No solution found.";
} else {
    var_dump($solution);
}