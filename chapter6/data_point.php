<?php

require_once "../data_structures.php";

class DataPoint
{
    private Sequence $originals;
    private Sequence $dimensions;

    public function __construct(Sequence $initial)
    {
        $this->originals = $initial;
        $this->dimensions = $initial;
    }

    public function getDimensions(): Sequence
    {
        return $this->dimensions;
    }

    public function setDimensions(Sequence $dimensions): void
    {
        $this->dimensions = $dimensions;
    }

    public function numDimensions(): int
    {
        return $this->dimensions->count();
    }

    function distance(DataPoint $other): float
    {
        $combined = $this->dimensions->zip($other->getDimensions());

        $differences = array_map(function($xy) {
            return ($xy[0] - $xy[1]) ** 2;
        }, $combined->toArray());

        return sqrt(array_sum($differences));
    }

    public function equals(DataPoint $other): bool
    {
        return $this->dimensions == $other->getDimensions();
    }
}