<?php

class Neuron
{
    private Sequence $weights;

    private float $learningRate;

    /**
     * @var callable
     */
    private $activationFunction;

    /**
     * @var callable
     */
    private $derivativeActivationFunction;

    private float $outputCache;
    private float $delta;

    public function __construct(
        Sequence $weights,
        float $learningRate,
        callable $activationFunction,
        callable $derivativeActivationFunction
    ) {
        $this->weights = $weights;
        $this->learningRate = $learningRate;
        $this->activationFunction = $activationFunction;
        $this->derivativeActivationFunction = $derivativeActivationFunction;
        $this->outputCache = 0.0;
        $this->delta = 0.0;
    }

    public function output(Sequence $inputs): float
    {
        $this->outputCache = dotProduct($inputs->toArray(), $this->weights->toArray());

        return $this->activationFunction($this->outputCache);
    }
}