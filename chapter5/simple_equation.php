<?php

require_once "../data_structures.php";
require_once "chromosome.php";
require_once "genetic_algorithm.php";

class SimpleEquation implements Chromosome
{
    private int $x;
    private int $y;

    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    /**
     * 6x - x^2 + 4y - y^2
     */
    public function fitness(): float
    {
        return 6 * $this->x - $this->x * $this->x + 4 * $this->y - $this->y * $this->y;
    }

    public static function randomInstance(): self
    {
        return new SimpleEquation(mt_rand(0, 100), mt_rand(0, 100));
    }

    public function crossover(Chromosome $other): array
    {
        $child1 = clone $this;
        /** @var SimpleEquation  $other */
        $child2 = clone $other;

        $child1->y = $other->getY();
        $child2->y = $this->y;

        return [$child1, $child2];
    }

    public function mutate(): void
    {
        if (mt_rand(1, 100) > 50) { // mutate x
            if (mt_rand(1, 100) > 50) {
                $this->x += 1;
            } else {
                $this->x -= 1;
            }
        } else {
            if (mt_rand(1, 100) > 50) { // mutate y
                $this->y += 1;
            } else {
                $this->y -= 1;
            }
        }
    }

    public function __toString(): string
    {
        return "X: {$this->x} Y: {$this->y} Fitness: {$this->fitness()}";
    }
}

$initialPopulation = TypedSequence::forType(SimpleEquation::class);

foreach (range(1, 20) as $item) {
    $initialPopulation->append(SimpleEquation::randomInstance());
}

$ga = new GeneticAlgorithm(
    $initialPopulation,
    13,
    100,
    10,
    70
);

/** @var SimpleEquation $result */
$result = $ga->run();

echo $result;