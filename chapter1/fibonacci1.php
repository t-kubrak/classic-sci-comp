<?php
function fib(int $n): int
{
    if ($n < 2) {
        return $n;
    }

    return fib($n - 2) + fib($n - 1);
}

echo fib(5) . "\n";
