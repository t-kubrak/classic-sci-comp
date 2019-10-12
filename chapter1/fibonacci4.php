<?php

function fib(int $n): Generator
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
        yield $next;
    }
}

$list = fib(10);

foreach ($list as $item) {
    echo  "{$item}\n";
}
