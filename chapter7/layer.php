<?php

require_once "../data_structures.php";
require_once "../functions.php";
require_once "neuron.php";

class Layer
{
    private ?Layer $previousLayer;

    /** @var TypedSequence|Neuron[] */
    private TypedSequence $neurons;

    /** @var Sequence|float[] */
    private Sequence $outputCache;

    public function __construct(
        ?Layer $previousLayer,
        int $numNeurons,
        float $learningRate,
        callable $activationFunction,
        callable $derivativeActivationFunction
    )
    {
        $this->previousLayer = $previousLayer;

        $this->neurons = $this->createNeurons(
            $previousLayer,
            $numNeurons,
            $learningRate,
            $activationFunction,
            $derivativeActivationFunction
        );

        $this->outputCache = new Sequence(
            array_map(function() {
                return 0.0;
            }, range(0, $numNeurons - 1))
        );
    }

    private function createNeurons(
        ?Layer $previousLayer,
        int $numNeurons,
        float $learningRate,
        callable $activationFunction,
        callable $derivativeActivationFunction
    ): TypedSequence
    {
        $neurons = TypedSequence::forType(Neuron::class);

        foreach (range(0, $numNeurons - 1) as $value) {
            $randomWeights = new Sequence();

            if ($previousLayer) {
                foreach (range(0, $previousLayer->getNeurons()->count() - 1) as $value) {
                    $randomWeights->append(randomFloat(0, 1));
                }
            }

            $neuron = new Neuron(
                $randomWeights,
                $learningRate,
                $activationFunction,
                $derivativeActivationFunction
            );

            $neurons->append($neuron);
        }

        return $neurons;
    }

    /**
     * @return Sequence|Neuron[]
     */
    public function getNeurons(): Sequence
    {
        return $this->neurons;
    }

    public function outputs(Sequence $inputs): Sequence
    {
        $this->outputCache = $inputs;

        if ($this->previousLayer) {
            $this->outputCache = array_map(function(Neuron $n) use ($inputs) {
                return $n->output($inputs);
            }, $this->neurons->toArray());
        }

        return $this->outputCache;
    }

    public function calculateDeltasForOutputLayer(Sequence $expected): void
    {
        foreach (range(0, $this->neurons->count() - 1) as $n) {
            $delta = $this->neurons[$n]->getDerivativeActivationFunction(
                $this->neurons[$n]->getOutputCache() * ($expected[$n] * $this->outputCache[$n])
            );

            $this->neurons[$n] = $this->neurons[$n]->withDelta($delta);
        }
    }

    public function calculateDeltasForHiddenLayer(Layer $nextLayer): void
    {
        $nextLayerNeurons = $nextLayer->getNeurons()->toArray();

        foreach ($this->neurons as $key => $neuron) {
            $nextWeigths = array_map(function($index, Neuron $n) {
                return $n->getWeights()[$index];
            }, array_keys($nextLayerNeurons), $nextLayerNeurons);

            $nextDeltas = array_map(function(Neuron $n) {
                return $n->getDelta();
            }, $nextLayerNeurons);

            $sumWeightsAndDeltas = dotProduct($nextWeigths, $nextDeltas);
            $delta = $neuron->getDerivativeActivationFunction($neuron->getOutputCache()) * $sumWeightsAndDeltas;

            $this->neurons[$key] = $this->neurons[$key]->withDelta($delta);
        }
    }
}