<?php

require_once "../data_structures.php";
require_once "board.php";

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
            $opposite->value = self::R;
        } elseif ($this->value == self::R) {
            $opposite->value = self::B;
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
        foreach (range($segmentLength - 1, $numRows - 1) as $r) {
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

class C4Board extends Board
{
    public const NUM_ROWS = 6;
    public const NUM_COLUMNS = 7;
    public const SEGMENT_LENGTH = 4;

    /** @var TypedSequence|Column[] */
    private TypedSequence $position;
    private C4Piece $turn;
    private Sequence $segments;

    public function __construct(TypedSequence $position = null, C4Piece $turn = null)
    {
        if (!$position) {
            $position = TypedSequence::forType(Column::class);

            for ($i = 0; $i < C4Board::NUM_COLUMNS; $i++) {
                $position->append(new Column());
            }
        }

        $this->position = $position;
        $this->turn = $turn ?? new C4Piece(C4Piece::B);

        $this->segments = generateSegments(self::NUM_COLUMNS, self::NUM_ROWS, self::SEGMENT_LENGTH);
    }

    public function turn(): C4Piece
    {
        return $this->turn;
    }

    public function move(Move $location): Board
    {
        $tempPosition = clone $this->position;

        foreach (range(0, self::NUM_COLUMNS -1) as $c) {
            $tempPosition[$c] = clone $this->position[$c];
        }

        $tempPosition[$location->getValue()]->push($this->turn);

        return new self($tempPosition, $this->turn()->opposite());
    }

    public function legalMoves(): TypedSequence
    {
        $moves = TypedSequence::forType(Move::class);

        foreach (range(0, self::NUM_COLUMNS - 1) as $c) {
            if (!$this->position[$c]->full()) {
                $moves->append(new Move($c));
            }
        }

        return $moves;
    }

    /**
     * Returns the count of black & red pieces in a segment
     */
    private function countSegment(Sequence $segment): Sequence
    {
        $blackCount = 0;
        $redCount = 0;

        foreach ($segment as $columnAndRow) {
            $column = $columnAndRow[0];
            $row = $columnAndRow[1];

            if ($this->position[$column]->getItem($row) == new C4Piece(C4Piece::B)) {
                $blackCount += 1;
            } elseif ($this->position[$column]->getItem($row) == new C4Piece(C4Piece::R)) {
                $redCount += 1;
            }
        }

        return new Sequence([$blackCount, $redCount]);
    }

    public function isWin(): bool
    {
        foreach ($this->segments as $segment) {
            [$blackCount, $redCount] = $this->countSegment($segment);

            if ($blackCount == 4 || $redCount == 4) {
                return true;
            }
        }

        return false;
    }

    private function evaluateSegment(Sequence $segment, Piece $player): float
    {
        [$blackCount, $redCount] = $this->countSegment($segment);

        if ($redCount > 0 && $blackCount > 0) {
            return 0;
        }

        $count = max($redCount, $blackCount);
        $score = 0;

        if ($count == 2) {
            $score = 1;
        } elseif ($count == 3) {
            $score = 100;
        } elseif ($count == 4) {
            $score = 1000000;
        }

        $color = C4Piece::B;

        if ($redCount > $blackCount) {
            $color = C4Piece::R;
        }

        if ($color != $player->getValue()) {
            return -$score;
        }

        return $score;
    }

    public function evaluate(Piece $player): float
    {
        $total = 0;

        foreach ($this->segments as $segment) {
            $total += $this->evaluateSegment($segment, $player);
        }

        return $total;
    }

    public function __toString(): string
    {
        $display = "";

        foreach (array_reverse(range(0, self::NUM_ROWS - 1)) as $r) {
            $display .= "|";

            foreach (range(0, self::NUM_COLUMNS - 1) as $c) {
                $display .= "{$this->position[$c]->getItem($r)}|";
            }

            $display .= PHP_EOL;
        }

        return $display;
    }
}