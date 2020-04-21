<?php

require_once "csp.php";
require_once "../data_structures.php";

class QueenConstraint extends Constraint
{
    private Sequence $columns;

    public function __construct(Sequence $columns)
    {
        parent::__construct($columns);

        $this->columns = $columns;
    }

    public function satisfied(Map $assignment): bool
    {
        foreach ($assignment->getIterator() as $q1c => $q1r) {
            for ($q2c = $q1c + 1; $q2c < $this->columns->count() + 1; $q2c++) {
                if (!$assignment->offsetExists($q2c)) {
                    continue;
                }

                $q2r = $assignment[$q2c];

                if ($q1r == $q2r) {
                    return false;
                }

                if (abs($q1r - $q2r) == abs($q1c - $q2c)) {
                    return false;
                }
            }
        }

        return true;
    }
}

$columns = new Sequence(range(1, 8));
$rows = TypedMap::forType('array');

foreach ($columns as $column) {
    $rows[$column] = range(1, 8);
}

$csp = new CSP($columns, $rows);
$csp->addConstraint(new QueenConstraint($columns));

$solution = $csp->backtrackingSearch();
if (!$solution) {
    echo "No solution found.";
} else {
    var_dump($solution);
}