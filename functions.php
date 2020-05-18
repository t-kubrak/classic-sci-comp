<?php

function randomFloat($min = 0, $max = 1) {
    return $min + mt_rand() / mt_getrandmax() * ($max - $min);
}

function permutations(array $elements): Generator
{
    if (count($elements) <= 1) {
        yield $elements;
    } else {
        foreach (permutations(array_slice($elements, 1)) as $permutation) {
            foreach (range(0, count($elements) - 1) as $i) {
                yield array_merge(
                    array_slice($permutation, 0, $i),
                    [$elements[0]],
                    array_slice($permutation, $i)
                );
            }
        }
    }
}

function cartesian(array $input)
{
    $result = [[]];
    foreach ($input as $key => $values) {
        $append = [];
        foreach ($values as $value) {
            foreach ($result as $data) {
                $append[] = $data + [$key => $value];
            }
        }
        $result = $append;
    }

    return $result;
}