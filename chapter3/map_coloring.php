<?php

require_once "csp.php";
require_once "../data_structures.php";

class MapColoringConstraint extends Constraint
{
    /**
     * @var string
     */
    private string $place1;
    /**
     * @var string
     */
    private string $place2;

    public function __construct(string $place1, string $place2)
    {
        parent::__construct(new ArrayObject([$place1, $place2]));
        $this->place1 = $place1;
        $this->place2 = $place2;
    }

    public function satisfied(Map $assignment): bool
    {
        if (!$assignment->offsetExists($this->place1)
            || !$assignment->offsetExists($this->place2)
        ) {
            return true;
        }

        return $assignment[$this->place1] != $assignment[$this->place2];
    }
}

$variables = new Sequence(["Western Australia", "Northern Territory", "South Australia",
    "Queensland", "New South Wales", "Victoria", "Tasmania"]);

$domains = TypedMap::forType(Sequence::class);

foreach ($variables as $variable) {
    $domains[$variable] = new Sequence(["red", "green", "blue"]);
}

$csp = new CSP($variables, $domains);

$csp->addConstraint(new MapColoringConstraint("Western Australia", "Northern Territory"));
$csp->addConstraint(new MapColoringConstraint("Western Australia", "South Australia"));
$csp->addConstraint(new MapColoringConstraint("South Australia", "Northern Territory"));
$csp->addConstraint(new MapColoringConstraint("Queensland", "Northern Territory"));
$csp->addConstraint(new MapColoringConstraint("Queensland", "South Australia"));
$csp->addConstraint(new MapColoringConstraint("Queensland", "New South Wales"));
$csp->addConstraint(new MapColoringConstraint("New South Wales", "South Australia"));
$csp->addConstraint(new MapColoringConstraint("Victoria", "South Australia"));
$csp->addConstraint(new MapColoringConstraint("Victoria", "New South Wales"));
$csp->addConstraint(new MapColoringConstraint("Victoria", "Tasmania"));

$solution = $csp->backtrackingSearch();

if (!$solution) {
    echo "No solution was found.";
} else {
    var_dump($solution);
}