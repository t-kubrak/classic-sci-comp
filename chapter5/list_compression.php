<?php

require_once "../data_structures.php";
require_once "chromosome.php";
require_once "genetic_algorithm.php";

class ListCompression implements Chromosome
{
    private Sequence $lst;

    public function __construct(Sequence $lst)
    {
        $this->lst = $lst;
    }

    public function getLst(): Sequence
    {
        return $this->lst;
    }

    public function setLst(Sequence $lst): void
    {
        $this->lst = $lst;
    }

    public function fitness(): float
    {
        return 1 / $this->bytesCompressed();
    }

    public static function randomInstance(): self
    {
        $people = ["Michael", "Sarah", "Joshua", "Narine", "David", "Sajid", "Melanie", "Daniel", "Wei", "Dean", "Brian", "Murat", "Lisa"];
        shuffle($people);
        return new self(new Sequence($people));
    }

    public function crossover(Chromosome $other): array
    {
        $child1 = clone $this;
        /** @var ListCompression $child2 */
        $child2 = clone $other;

        [$idx1, $idx2] = array_rand(range(0, $this->lst->count() - 1), 2);
        $child1Letters = $child1->getLst();
        $child2Letters = $child2->getLst();
        $l1 = $child1Letters[$idx1];
        $l2 = $child2Letters[$idx2];

        $child1Letters[$child1Letters->index($l2)] = $child1Letters[$idx2];
        $child1Letters[$idx2] = $l2;
        $child2Letters[$child2Letters->index($l1)] = $child2Letters[$idx1];
        $child2Letters[$idx1] = $l1;

        $child1->setLst($child1Letters);
        $child2->setLst($child2Letters);

        return [$child1, $child2];
    }

    public function mutate(): void
    {
        [$idx1, $idx2] = array_rand(range(0, $this->lst->count() - 1), 2);

        $old = $this->lst[$idx1];
        $this->lst[$idx1] = $this->lst[$idx2];
        $this->lst[$idx2] = $old;
    }

    private function bytesCompressed(): int
    {
        $listString = implode($this->lst->toArray());

        $before = memory_get_usage();

        $listString = strlen(gzcompress($listString));

        $after = memory_get_usage();

        $diff = abs($before - $after);

        return $diff;
    }

    public function __toString(): string
    {
        return "Order: " . implode(", ", $this->lst->toArray()) . " Bytes: {$this->bytesCompressed()}";
    }
}

$initialPopulation = TypedSequence::forType(ListCompression::class);

foreach (range(1, 100) as $item) {
    $initialPopulation->append(ListCompression::randomInstance());
}

$ga = new GeneticAlgorithm(
    $initialPopulation,
    1,
    100,
    20,
    70,
    SelectionType::TOURNAMENT
);

/** @var SendMoreMoney2 $result */
$result = $ga->run();

echo $result;



