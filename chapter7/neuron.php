<?php

require_once "../data_structures.php";
require_once "util.php";

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

    public function withDelta(float $delta): self
    {
        $neuron = clone $this;
        $neuron->delta = $delta;
        return $neuron;
    }

    public function withWeight(int $index, float $weight): self
    {
        $neuron = clone $this;
        $neuron->weights[$index] = $weight;
        return $neuron;
    }

    public function getDerivativeActivationFunction(float $input): float
    {
        return $this->derivativeActivationFunction($input);
    }

    public function getOutputCache(): float
    {
        return $this->outputCache;
    }

    public function getWeights(): Sequence
    {
        return $this->weights;
    }

    public function getDelta(): float
    {
        return $this->delta;
    }

    public function getLearningRate(): float
    {
        return $this->learningRate;
    }
}