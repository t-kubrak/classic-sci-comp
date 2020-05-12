<?php

function zScores(Sequence $original)
{
    $avg = $original->mean();
    $std = $original->pstDev();

    if ($std == 0) {
        return array_map(function(){
            return 0;
        }, $original->toArray());
    }

    return  array_map(function ($x) use ($std, $avg) {
        return ($x - $avg) / $std;
    }, $original->toArray());
}