<?php

require_once "minimax.php";
require_once "connectfour.php";

$board = new C4Board();

function getPlayerMove(Board $board): Move
{
    $playerMove = new Move(-1);

    while (!$board->legalMoves()->has($playerMove)) {
        $play = readline("Enter a legal square (0-8):");
        $playerMove = new Move($play);
    }

    return $playerMove;
}

// main game loop
while (true) {
    $humanMove = getPlayerMove($board);
    $board = $board->move($humanMove);

    if ($board->isWin()) {
        echo "Human wins!".PHP_EOL;
        break;
    } elseif ($board->isDraw()) {
        echo "Draw!".PHP_EOL;
        break;
    }

    $computerMove = findBestMove($board, 3);
    echo "Computer move is {$computerMove}".PHP_EOL;
    $board = $board->move($computerMove);
    echo $board;

    if ($board->isWin()) {
        echo "Computer wins!".PHP_EOL;
        break;
    } elseif ($board->isDraw()) {
        echo "Draw!".PHP_EOL;
        break;
    }
}