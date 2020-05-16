<?php

/** Find the best possible outcome for original player */
function minimax(Board $board, bool $maximizing, Piece $originalPlayer, int $maxDepth = 8): float
{
    // Base case – terminal position or maximum depth reached
    if ($board->isWin() || $board->isDraw() || $maxDepth == 0) {
        return $board->evaluate($originalPlayer);
    }

    // Recursive case - maximize your gains or minimize the opponent's gains
    if ($maximizing) {
        $bestEval = -INF;

        foreach ($board->legalMoves() as $move) {
            $result = minimax($board->move($move), false, $originalPlayer, $maxDepth - 1);
            $bestEval = max($result, $bestEval);
        }

        return $bestEval;
    } else {
        $worstEval = INF;

        foreach ($board->legalMoves() as $move) {
            $result = minimax($board->move($move), true, $originalPlayer, $maxDepth - 1);
            $worstEval = min($result, $worstEval);
        }

        return $worstEval;
    }
}

function findBestMove(Board $board, int $maxDepth = 8): Move
{
    $bestEval = -INF;
    $bestMove = new Move(-1);

    foreach ($board->legalMoves() as $move) {
        $result = minimax($board->move($move), false, $board->turn(), $maxDepth);

        if ($result > $bestEval) {
            $bestEval = $result;
            $bestMove = $move;
        }
    }

    return $bestMove;
}