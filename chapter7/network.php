<?php

require_once "../data_structures.php";
require_once "util.php";
require_once "layer.php";

class Network
{
    /** @var Sequence|Layer[] */
    private Sequence $layers;

    public function __construct(
        Sequence $layerStructure,
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
        Sequence $layerStructure,
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

    /**
     * Pushes input data to the first layer, then output from the first
     * as input to the second, second to the third, etc.
     *
     * @param Sequence $input
     * @return Sequence
     */
    public function outputs(Sequence $input): Sequence
    {
        $result = array_reduce(
            $this->layers->toArray(),
            fn(Sequence $inputs, Layer $layer) => $layer->outputs($inputs),
            $input
        );

        return $result;
    }

    /**
     * Figure out each neuron's changes based on the errors of the output
     * versus the expected outcome
     */
    public function backpropagate(Sequence $expected): void
    {
        $lastLayer = $this->layers->count() - 1;
        $this->layers[$lastLayer]->calculateDeltasForOutputLayer($expected);

        /** calculate delta for hidden layers in reverse order */
        foreach (range($lastLayer - 1, 0, -1) as $l) {
            $this->layers[$l]->calculateDeltasForHiddenLayer($this->layers[$l + 1]);
        }
    }

    /**
     * backpropagate() doesn't actually change any weights
     * this function uses the deltas calculated in backpropagate() to
     * actually make changes to the weights
     */
    public function updateWeights(): void
    {
        /** @var Layer[] $layers */
        $layers = $this->layers->toArray();
        next($layers);

        foreach ($layers as &$layer) {
            foreach ($layer->getNeurons() as &$neuron) {
                foreach (range(0, $neuron->getWeights()->count() - 1) as $w) {
                    $weight = $neuron->getWeights()[$w]
                        + ($neuron->getLearningRate()
                            * ($layer->getPreviousLayer()->getOutputCache()[$w]) * $neuron->getDelta()
                        );

                    $neuron = $neuron->withWeight($w, $weight);
                }
            }
        }
    }

    /**
     * train() uses the results of outputs() run over many inputs and compared
     * against expecteds to feed backpropagate() and update_weights()
     */
    public function train(Sequence $inputs, Sequence $expected): void
    {
        foreach ($inputs as $location => $xs) {
            $ys = $expected[$location];
            $outs = $this->outputs($xs);
            $this->backpropagate($ys);
            $this->updateWeights();
        }
    }

    /**
     * for generalized results that require classification this function will return
     * the correct number of trials and the percentage correct out of the total
     */
    public function validate(Sequence $inputs, Sequence $expected, callable $interpretOutput): array
    {
        $correct = 0;

        foreach ($inputs->zip($expected) as $inputAndExpected) {
            $input = $inputAndExpected[0];
            $expected = $inputAndExpected[1];
            $result = $interpretOutput($this->outputs($input));

            if ($result == $expected) {
                $correct += 1;
            }
        }

        $percentage = $correct / $inputs->count();

        return [$correct, $inputs->count(), $percentage];
    }
}