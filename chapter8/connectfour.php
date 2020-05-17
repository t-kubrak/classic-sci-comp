<?php

class C4Piece implements Piece
{
    public const B = "B";
    public const R = "R";
    public const E = " "; //empty
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function opposite(): self
    {
        $opposite = clone $this;

        if ($this->value == self::B) {
            $opposite->value = self::B;
        } elseif ($this->value == self::R) {
            $opposite->value = self::R;
        } else {
            $opposite->value = self::E;
        }

        return $opposite;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

function generateSegments(int $numColumns, int $numRows, int $segmentLength): Sequence
{
    $segments = new Sequence();

    // generate vertical segments
    foreach (range(0, $numColumns - 1) as $c) {
        foreach (range(0, $numRows - $segmentLength) as $r) {
            $segment = new Sequence();

            foreach (range(0, $segmentLength - 1) as $t) {
                $segment->append(new Sequence([$c, $r + $t]));
            }

            $segments->append($segment);
        }
    }

    // generate horizontal segments
    foreach (range(0, $numColumns - $segmentLength) as $c) {
        foreach (range(0, $numRows - 1) as $r) {
            $segment = new Sequence();

            foreach (range(0, $segmentLength - 1) as $t) {
                $segment->append(new Sequence([$c + $t, $r]));
            }

            $segments->append($segment);
        }
    }

    // generate the bottom left to top right diagonal segments
    foreach (range(0, $numColumns - $segmentLength) as $c) {
        foreach (range(0, $numRows - $segmentLength) as $r) {
            $segment = new Sequence();

            foreach (range(0, $segmentLength - 1) as $t) {
                $segment->append(new Sequence([$c + $t, $r + $t]));
            }

            $segments->append($segment);
        }
    }

    // generate the top left to bottom right diagonal segments
    foreach (range(0, $numColumns - $segmentLength) as $c) {
        foreach (range($segmentLength - 2, $numRows) as $r) {
            $segment = new Sequence();

            foreach (range(0, $segmentLength - 1) as $t) {
                $segment->append(new Sequence([$c + $t, $r - $t]));
            }

            $segments->append($segment);
        }
    }

    return $segments;
}

class Column
{
    private TypedSequence $container;

    public function __construct()
    {
        $this->container = TypedSequence::forType(C4Piece::class);
    }

    public function full(): bool
    {
        return $this->container->count() == C4Board::NUM_ROWS;
    }

    public function push(C4Piece $item): void
    {
        if ($this->full()) {
            throw new LogicException("Trying to push piece to full column");
        }

        $this->container->append($item);
    }

    public function getItem(int $index): C4Piece
    {
        if ($index > $this->container->count() - 1) {
            return new C4Piece(C4Piece::E);
        }

        return $this->container[$index];
    }

    public function __clone()
    {
        $this->container = clone $this->container;
    }

    public function copy(): self
    {
        return new self();
    }
}
