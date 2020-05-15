<?php

require_once "../data_structures.php";
require_once "board.php";

class TTTPiece implements Piece
{
    public const X = "X";
    public const O = "O";
    public const E = " "; // empty

    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function opposite(): self
    {
        $opposite = clone $this;

        if ($this->value == self::X) {
            $opposite->value = self::O;
        } elseif ($this->value == self::O) {
            $opposite->value = self::X;
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

class TTTBoard extends Board
{
    private TypedSequence $position;
    private TTTPiece $turn;

    public function __construct(TypedSequence $position = null, TTTPiece $turn = null)
    {
        if (!$position) {
            $position = TypedSequence::forType(TTTPiece::class);

            foreach (range(1, 9) as $item) {
                $position->append(new TTTPiece(TTTPiece::E));
            }
        }

        $this->position = $position;
        $this->turn = $turn ?? new TTTPiece(TTTPiece::X);
    }

    public function turn(): Piece
    {
        return $this->turn;
    }

    public function move(Move $location):Board
    {
        $tempPosition = clone $this->position;
        $tempPosition[$location->getValue()] = $this->turn;

        return new self($tempPosition, $this->turn->opposite());
    }

    public function legalMoves(): TypedSequence
    {
        $moves = TypedSequence::forType(Move::class);

        foreach (range(0, $this->position->count() - 1) as $l) {
            if ($this->position[$l] == TTTPiece::E) {
                $moves->append(new Move($l));
            }
        }

        return $moves;
    }

    /**
     * 3 rows, 3 columns and 2 diagonal checks
     */
    public function isWin(): bool
    {
        return $this->position[0] == $this->position[1] && $this->position[0] == $this->position[2] && $this->position[0] != TTTPiece::E ||
            $this->position[3] == $this->position[4] && $this->position[3] == $this->position[5] && $this->position[3] != TTTPiece::E ||
            $this->position[6] == $this->position[7] && $this->position[6] == $this->position[8] && $this->position[6] != TTTPiece::E ||
            $this->position[0] == $this->position[3] && $this->position[0] == $this->position[6] && $this->position[0] != TTTPiece::E ||
            $this->position[1] == $this->position[4] && $this->position[1] == $this->position[7] && $this->position[1] != TTTPiece::E ||
            $this->position[2] == $this->position[5] && $this->position[2] == $this->position[8] && $this->position[2] != TTTPiece::E ||
            $this->position[0] == $this->position[4] && $this->position[0] == $this->position[8] && $this->position[0] != TTTPiece::E ||
            $this->position[2] == $this->position[4] && $this->position[2] == $this->position[6] && $this->position[2] != TTTPiece::E;
    }

    public function evaluate(Piece $player): float
    {
        if ($this->isWin() && $this->turn == $player) {
            return -1;
        } elseif ($this->isWin() && $this->turn != $player) {
            return 1;
        }

        return 0;
    }

    public function __toString(): string
    {
        return "{$this->position[0]}|{$this->position[1]}|{$this->position[2]}\n
            -----\n
            {$this->position[3]}|{$this->position[4]}|{$this->position[5]}\n
            -----\n
            {$this->position[6]}|{$this->position[7]}|{$this->position[8]}\n";
    }
}
