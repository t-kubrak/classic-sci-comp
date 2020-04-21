<?php

require_once "csp.php";
require_once "../data_structures.php";

class SendMoreMoneyConstraint extends Constraint
{
    private Sequence $letters;

    public function __construct(Sequence $letters)
    {
        parent::__construct($letters);

        $this->letters = $letters;
    }

    public function satisfied(Map $assignment): bool
    {
        $assignmentSet = new Ds\Set($assignment);

        if ($assignmentSet->count() < $assignment->count()) {
            return false;
        }

        if ($assignment->count() == $this->letters->count()) {
            $s = $assignment["S"];
            $e = $assignment["E"];
            $n = $assignment["N"];
            $d = $assignment["D"];
            $m = $assignment["M"];
            $o = $assignment["O"];
            $r = $assignment["R"];
            $y = $assignment["Y"];

            $send = $s * 1000 + $e * 100 + $n * 10 + $d;
            $more = $m * 1000 + $o * 100 + $r * 10 + $e;
            $money = $m * 10000 + $o * 1000 + $n * 100 + $e * 10 + $y;

            return $send + $more == $money;
        }

        return true;
    }
}

$letters = new Sequence(["S", "E", "N", "D", "M", "O", "R", "Y"]);
$possibleDigits = TypedMap::forType('array');

foreach ($letters as $letter) {
    $possibleDigits[$letter] = range(0, 9);
}

$possibleDigits["M"] = [1];

$csp = new CSP($letters, $possibleDigits);
$csp->addConstraint(new SendMoreMoneyConstraint($letters));

$solution = $csp->backtrackingSearch();

if (!$solution) {
    echo "No solution was found.";
} else {
    var_dump($solution);
}