<?php

require_once "../data_structures.php";

interface Piece
{
    public function opposite(): self;
}

abstract class Board
{
    abstract function turn(): Piece;

    abstract function move(): Board;

    abstract function legalMoves(): TypedSequence;

   abstract function isWin(): bool;

   protected function isDraw(): bool
   {
       return !$this->isWin() && $this->legalMoves()->count() == 0;
   }

   abstract function evaluate(Piece $player): float;
}