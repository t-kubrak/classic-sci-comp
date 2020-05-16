<?php

require_once "../data_structures.php";
require_once "minimax.php";
require_once "tictactoe.php";

class TTTMinimaxTestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * win in 1 move
     *
     *  XOX
     *  X O
     *    O
     */
    public function testEasyPosition()
    {
        $position = TypedSequence::forType(TTTPiece::class)
            ->append(new TTTPiece(TTTPiece::X))
            ->append(new TTTPiece(TTTPiece::O))
            ->append(new TTTPiece(TTTPiece::X))
            ->append(new TTTPiece(TTTPiece::X))
            ->append(new TTTPiece(TTTPiece::E))
            ->append(new TTTPiece(TTTPiece::O))
            ->append(new TTTPiece(TTTPiece::E))
            ->append(new TTTPiece(TTTPiece::E))
            ->append(new TTTPiece(TTTPiece::O));

        $testBoard = new TTTBoard($position, new TTTPiece(TTTPiece::X));
        $answer = findBestMove($testBoard);

        $this->assertEquals(6, $answer->getValue());
    }

    /**
     * must block O's win
     *
     * X
     *   O
     *  XO
     */
    public function testBlockPosition()
    {
        $position = TypedSequence::forType(TTTPiece::class)
            ->append(new TTTPiece(TTTPiece::X))
            ->append(new TTTPiece(TTTPiece::E))
            ->append(new TTTPiece(TTTPiece::E))
            ->append(new TTTPiece(TTTPiece::E))
            ->append(new TTTPiece(TTTPiece::E))
            ->append(new TTTPiece(TTTPiece::O))
            ->append(new TTTPiece(TTTPiece::E))
            ->append(new TTTPiece(TTTPiece::X))
            ->append(new TTTPiece(TTTPiece::O));

        $testBoard = new TTTBoard($position, new TTTPiece(TTTPiece::X));
        $answer = findBestMove($testBoard);

        $this->assertEquals(2, $answer->getValue());
    }

    /**
     * find the best move to win 2 moves
     *
     * X
     *   O
     * OX
     */
    public function testHardPosition()
    {
        $position = TypedSequence::forType(TTTPiece::class)
            ->append(new TTTPiece(TTTPiece::X))
            ->append(new TTTPiece(TTTPiece::E))
            ->append(new TTTPiece(TTTPiece::E))
            ->append(new TTTPiece(TTTPiece::E))
            ->append(new TTTPiece(TTTPiece::E))
            ->append(new TTTPiece(TTTPiece::O))
            ->append(new TTTPiece(TTTPiece::O))
            ->append(new TTTPiece(TTTPiece::X))
            ->append(new TTTPiece(TTTPiece::E));

        $testBoard = new TTTBoard($position, new TTTPiece(TTTPiece::X));
        $answer = findBestMove($testBoard);

        $this->assertEquals(1, $answer->getValue());
    }
}