<?php

function fib(int $n): int
{
    if ($n == 0) {
        return $n;
    }

    $last = 0;
    $next = 1;

    for ($counter = 1; $counter < $n; $counter++) {
        $lastOld = $last;

        $last = $next;
        $next += $lastOld;
    }

    return $next;
}

echo fib(10) . "\n";
