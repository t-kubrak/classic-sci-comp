<?php

require_once "../data_structures.php";
require_once "chromosome.php";

abstract class SelectionType
{
    public const ROULETTE = "ROULETTE";
    public const TOURNAMENT = "TOURNAMENT";
}

class GeneticAlgorithm
{
    /**
     * @var Sequence|Chromosome[]
     */
    private Sequence $population;
    private float $threshold;
    private int $maxGenerations;
    private float $mutationChance;
    private float $crossoverChance;
    private string $selectionType;
    private $fitnessKey;

    public function __construct(
        Sequence $initialPopulation,
        float $threshold,
        int $maxGenerations = 100,
        int $mutationChance = 1,
        int $crossoverChance = 70,
        string $selectionType = SelectionType::TOURNAMENT
    )
    {
        $this->population = $initialPopulation;
        $this->threshold = $threshold;
        $this->maxGenerations = $maxGenerations;
        $this->mutationChance = $mutationChance;
        $this->crossoverChance = $crossoverChance;
        $this->selectionType = $selectionType;
        $this->fitnessKey = [$this->population[0], 'fitness'];
    }

    /**
     * TODO
     * Use the probability distribution wheel to pick 2 parents
     *
     * @param array $wheel
     * @return array
     */
    public function pickRoulette(array $wheel): array
    {
        $pick1 = $this->population[mt_rand(0, $this->population->count() - 1)];
        $pick2 = $this->population[mt_rand(0, $this->population->count() - 1)];

        return [$pick1, $pick2];
    }

    /**
     * TODO
     * Choose num_participants at random and take the best 2
     *
     * @param int $numParticipants
     * @return array
     */
    public function pickTournament(int $numParticipants): array
    {
        $pick1 = $this->population[mt_rand(0, $this->population->count() - 1)];
        $pick2 = $this->population[mt_rand(0, $this->population->count() - 1)];

        return [$pick1, $pick2];
    }

    /**
     *  Replace the population with a new generation of individuals
     */
    public function reproduceAndReplace()
    {
        $newPopulation = new Sequence();

        //keep going until we've filled the new generation
        while($newPopulation->count() < $this->population->count()) {
            // pick 2 parents
            if ($this->selectionType == SelectionType::ROULETTE) {
                $wheel = array_map(function(Chromosome $chromosome) {
                    $chromosome->fitness();
                }, $this->population->toArray());

                $parents = $this->pickRoulette($wheel);
            } else {
                $parents = $this->pickTournament(intdiv($this->population->count(), 2));
            }

            // potentially crossover 2 parents
            if (mt_rand(0, 100) < $this->crossoverChance) {
                $newPopulation->merge($parents[0]->crossover($parents[1]));
            } else {
                $newPopulation->merge($parents);
            }
        }

        // if we had an odd number, we'll have 1 extra, so we remove it
        if ($newPopulation->count() > $this->population->count()) {
            $newPopulation->pop();
        }

        $this->population = $newPopulation;
    }

    /**
     * With mutationChance probability mutate each individual
     */
    public function mutate(): void
    {
        $population = array_map(function(Chromosome $individual) {
            if (rand(0, 100) < $this->mutationChance) {
                $individual = $individual->mutate();
            }

            return $individual;
        }, $this->population->toArray());

        $this->population = new Sequence($population);
    }

    /**
     * Run the genetic algorithm for max_generations iterations
     * and return the best individual found
     */
    public function run(): Chromosome
    {
        /** @var Chromosome $best */
        $best = maxBy($this->population, $this->fitnessKey);

        foreach (range(0, $this->maxGenerations - 1) as $generation) {
            if ($best->fitness() >= $this->threshold) {
                return $best;
            }

            echo "Generation {$generation} Best {$best->fitness()} Avg " . meanBy($this->population, $this->fitnessKey);

            $this->reproduceAndReplace();

            $this->mutate();

            /** @var Chromosome $highest */
            $highest = maxBy($this->population, $this->fitnessKey);

            if ($highest->fitness() > $best->fitness()) {
                $best = $highest;
            }
        }

        return $best;
    }
}

/** TODO */
function maxBy(Sequence $values, callable $property)
{
    $propertyValues = array_map(function(Chromosome $individual) {
        return $individual->fitness();
    }, $values->toArray());

    return max($propertyValues);
}

/** TODO */
function meanBy(Sequence $values, $property)
{
    $propertyValues = array_map(function(Chromosome $individual) {
        return $individual->fitness();
    }, $values->toArray());

    return array_sum($propertyValues) / count($propertyValues);
}