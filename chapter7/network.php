<?php

require_once "../data_structures.php";
require_once "util.php";

class Network
{
    /** @var Sequence|Layer[] */
    private Sequence $layers;

    public function __construct(
        TypedSequence $layerStructure,
        float $learningRate,
        callable $activationFunction = null,
        callable $derivativeActivationFunction = null
    )
    {
        if ($layerStructure->count() < 3) {
            throw new InvalidArgumentException("Should be at least 3 layers (1 input, 1 hidden, 1 output)");
        }

        $activationFunction = $activationFunction ?? 'sigmoid';
        $derivativeActivationFunction = $derivativeActivationFunction ?? 'derivativeSigmoid';

        $this->layers = $this->layersFrom(
             $layerStructure,
             $learningRate,
             $activationFunction,
             $derivativeActivationFunction
        );
    }

    private function layersFrom(
        TypedSequence $layerStructure,
        float $learningRate,
        callable $activationFunction,
        callable $derivativeActivationFunction
    ): Sequence
    {
        $layers = new Sequence();

        $inputLayer = new Layer(
            null,
            $layerStructure[0],
            $learningRate,
            $activationFunction,
            $derivativeActivationFunction
        );

        $layers->append($inputLayer);

        // hidden layers and output layer
        foreach (range(1, $layerStructure->count() - 1) as $key) {
            $previous = $layerStructure[$key - 1];
            $numNeurons = $layerStructure[$key];

            $nextLayer = new Layer(
                $this->layers[$previous],
                $numNeurons,
                $learningRate,
                $activationFunction,
                $derivativeActivationFunction
            );

            $layers->append($nextLayer);
        }

        return $layers;
    }
}