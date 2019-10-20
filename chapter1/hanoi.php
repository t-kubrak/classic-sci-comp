<?php

class Stack extends SplStack
{

}

$numOfDiscs = 4;
$towerA = new Stack();
$towerB = new Stack();
$towerC = new Stack();

for ($i = 1; $i <= $numOfDiscs; $i++) {
    $towerA->push($i);
}

function hanoi(Stack $begin, Stack $end, Stack $temp, $n): void
{
    if ($n == 1) {
        $end->push($begin->pop());
    } else {
        hanoi($begin, $temp, $end, $n - 1);
        hanoi($begin, $end, $temp, 1);
        hanoi($temp, $end, $begin, $n - 1);
    }
}

hanoi($towerA, $towerC, $towerB, $numOfDiscs);

print_r($towerA);
print_r($towerB);
print_r($towerC);

