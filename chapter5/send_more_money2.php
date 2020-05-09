<?php

class SendMoreMoney2 implements Chromosome
{
    private Sequence $letters;

    public function __construct(Sequence $letters)
    {
        $this->letters = $letters;
    }

    public function getLetters(): Sequence
    {
        return $this->letters;
    }

    public function setLetters(Sequence $letters): void
    {
        $this->letters = $letters;
    }

    public function fitness(): float
    {
        $s = $this->letters->index("S");
        $e = $this->letters->index("E");
        $n = $this->letters->index("N");
        $d = $this->letters->index("D");
        $m = $this->letters->index("M");
        $o = $this->letters->index("O");
        $r = $this->letters->index("R");
        $y = $this->letters->index("Y");

        $send = $s * 1000 + $e * 100 + $n * 10 + $d;
        $more = $m * 1000 + $o * 100 + $r * 10 + $e;
        $money = $m * 10000 + $o * 1000 + $n * 100 + $e * 10 + $y;

        $difference = abs($money - ($send + $more));

        return 1/($difference / 1);
    }

    public static function randomInstance(): self
    {
        $letters = ["S", "E", "N", "D", "M", "O", "R", "Y", " ", " "];
        shuffle($letters);
        return new SendMoreMoney2(new Sequence($letters));
    }

    public function crossover(Chromosome $other): array
    {
        $child1 = clone $this;
        /** @var SendMoreMoney2 $child2 */
        $child2 = clone $other;

        [$idx1, $idx2] = array_rand(range(0, $this->letters - 1), 2);
        $child1Letters = $child1->getLetters();
        $child2Letters = $child2->getLetters();
        $l1 = $child1Letters[$idx1];
        $l2 = $child2Letters[$idx2];

        $child1Letters[$child1Letters->index($l2)] = $child1Letters[$idx2];
        $child1Letters[$idx2] = $l2;
        $child2Letters[$child2Letters->index($l1)] = $child2Letters[$idx1];
        $child2Letters[$idx1] = $l1;

        $child1->setLetters($child1Letters);
        $child2->setLetters($child2Letters);

        return [$child1, $child2];
    }

    public function mutate(): void
    {
        [$idx1, $idx2] = array_rand(range(0, $this->letters - 1), 2);

        $old = $this->letters[$idx1];
        $this->letters[$idx1] = $this->letters[$idx2];
        $this->letters[$idx2] = $old;
    }

    public function __toString(): string
    {
        $s = $this->letters->index("S");
        $e = $this->letters->index("E");
        $n = $this->letters->index("N");
        $d = $this->letters->index("D");
        $m = $this->letters->index("M");
        $o = $this->letters->index("O");
        $r = $this->letters->index("R");
        $y = $this->letters->index("Y");

        $send = $s * 1000 + $e * 100 + $n * 10 + $d;
        $more = $m * 1000 + $o * 100 + $r * 10 + $e;
        $money = $m * 10000 + $o * 1000 + $n * 100 + $e * 10 + $y;

        $difference = abs($money - ($send + $more));

        return "{$send} + {$more} = {$money} Difference: {$difference} \n";
    }
}

$initialPopulation = TypedSequence::forType(SendMoreMoney2::class);

foreach (range(1, 1000) as $item) {
    $initialPopulation->append(SendMoreMoney2::randomInstance());
}

$ga = new GeneticAlgorithm(
    $initialPopulation,
    1,
    1000,
    20,
    70,
    SelectionType::ROULETTE
);

/** @var SendMoreMoney2 $result */
$result = $ga->run();

echo $result;
