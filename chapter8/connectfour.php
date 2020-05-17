<?php

class C4Piece implements Piece
{
    private const B = "B";
    private const R = "R";
    private const E = " "; //empty
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