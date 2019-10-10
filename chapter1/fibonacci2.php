<?php
$memo = [0 => 0, 1 => 1];

function fib(int $n): int
{
    global $memo;

    if (!isset($memo[$n]) && $n >= 2) {
        $memo[$n] = fib($n - 2) + fib($n - 1);
    }

    return $memo[$n];
}

echo fib(50) . "\n";
