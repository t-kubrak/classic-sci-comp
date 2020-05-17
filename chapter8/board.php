<?php

require_once "../data_structures.php";

class Move
{
    private int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return strval($this->value);
    }
}

interface Piece
{
    public function opposite(): self;
}

abstract class Board
{
    abstract function turn(): Piece;

    abstract function move(Move $location): Board;

    abstract function legalMoves(): TypedSequence;

    abstract function isWin(): bool;

    public function isDraw(): bool
    {
        return !$this->isWin() && $this->legalMoves()->count() == 0;
    }

    abstract function evaluate(Piece $player): float;
}