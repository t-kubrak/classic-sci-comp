<?php

function dotProduct(array $xs, array $ys): float
{
    return array_sum(array_map(function($x, $y) {
        return $x * $y;
    }, $xs, $ys));
}

function sigmoid(float $x): float
{
    return 1 / (1 + exp(-$x));
}

function derivativeSigmoid(float $x): float
{
    $sig = sigmoid($x);

    return $sig * (1 - $sig);
}