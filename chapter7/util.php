<?php

function dotProduct(array $xs, array $ys): float
{
    return array_sum(array_map(fn($x, $y) => $x * $y, $xs, $ys));
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

/**
 * @param Sequence[]|Sequence $dataset
 */
function normalizeByFeatureScaling(Sequence $dataset)
{
    foreach (range(0, $dataset[0]->count() - 1) as $colNum) {
        $column = array_map(
            fn($row) => $row[$colNum],
            $dataset->toArray()
        );

        $maximum = max($column);
        $minimum = min($column);

        foreach (range(0, $dataset->count() - 1) as $rowNum) {
            $dataset[$rowNum][$colNum] = ($dataset[$rowNum][$colNum] - $minimum) / ($maximum - $minimum);
        }
    }
}